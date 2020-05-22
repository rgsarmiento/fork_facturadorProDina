<?php

namespace App\Http\Controllers\Tenant;

use Facades\App\Models\Tenant\Document as FacadeDocument;
use App\Http\Requests\Tenant\DocumentRequest;
use App\Traits\Tenant\DocumentTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\{
    TypeIdentityDocument,
    DetailDocument,
    TypeDocument,
    TypeInvoice,
    NoteConcept,
    Document,
    Currency,
    Company,
    Client,
    Item,
    Tax,
    PaymentMethod,
    PaymentForm
};
use Carbon\Carbon;
use Mpdf\Mpdf;
use DB;
use App\Models\TenantService\{
    Company as ServiceTenantCompany
};
use App\Models\TenantService\{
    Company as TenantServiceCompany
};
use App\Mail\Tenant\SendGraphicRepresentation;
use Illuminate\Support\Facades\Mail;
use DateTime;
use Storage;


class DocumentController extends Controller
{
    use DocumentTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('document.tenant.index');
    }

    public function create() {
/*        $company = Company::with('type_regime', 'type_identity_document')->firstOrFail();
        return json_encode($company);   */

        return view('document.tenant.create');
    }

    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return [
            'payment_methods' => PaymentMethod::all(),
            'payment_forms' => PaymentForm::all(),
            'typeDocuments' => $typeDocuments = TypeDocument::query()
                ->get()
                ->each(function($typeDocument) {
                    $typeDocument->alert_range = (($typeDocument->to - 100) < (Document::query()
                        ->hasPrefix($typeDocument->prefix)
                        ->whereBetween('number', [$typeDocument->from, $typeDocument->to])
                        ->max('number') ?? $typeDocument->from));

                    $typeDocument->alert_date = ($typeDocument->resolution_date_end == null) ? false : Carbon::parse($typeDocument->resolution_date_end)->subMonth(1)->lt(Carbon::now());
                }),
            'typeInvoices' => TypeInvoice::all(),
            'documents' => Document::query()
                ->with('state_document', 'currency', 'type_document', 'detail_documents', 'reference', 'log_documents')
                ->get(),
            'currencies' => Currency::all(),
            'clients' => Client::all(),
            'items' => Item::query()
                ->with('typeUnit', 'tax')
                ->get(),
            'taxes' => Tax::all(),
            'companyservice' => ServiceTenantCompany::first()
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Controllers\Tenant\DocumentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request) {
        DB::connection('tenant')->beginTransaction();

        try {

            //envio api ///

            $response =  null;
            $response_status =  null;
            $correlative_api = $this->getCorrelativeInvoice(1);

            // return $correlative_api;

            if(!is_numeric($correlative_api)){
                return [
                    'success' => false,
                    'message' => 'Error al obtener correlativo Api.'
                ];
            }

            $service_invoice = $request->service_invoice;
            $service_invoice['number'] = $correlative_api;

            $datoscompany = Company::with('type_regime', 'type_identity_document')->firstOrFail();
            $company = ServiceTenantCompany::firstOrFail();

//            return json_encode($request->date_expiration);

            if(file_exists(storage_path('sendmail.api')))
                $service_invoice['sendmail'] = true;
            $service_invoice['ivaresponsable'] = $datoscompany->type_regime->name;
            $service_invoice['nombretipodocid'] = $datoscompany->type_identity_document->name;
            $service_invoice['tarifaica'] = $datoscompany->ica_rate;
            $service_invoice['actividadeconomica'] = $datoscompany->economic_activity_code;
            $service_invoice['notes'] = $request->observation;
            $service_invoice['date'] = date('Y-m-d');
            $service_invoice['time'] = date('H:i:s');
            $service_invoice['payment_form']['payment_form_id'] = $request->payment_form_id;
            $service_invoice['payment_form']['payment_method_id'] = $request->payment_method_id;
            if($request->payment_form_id == '1')
                $service_invoice['payment_form']['payment_due_date'] = date('Y-m-d');
            else
                $service_invoice['payment_form']['payment_due_date'] = date('Y-m-d', strtotime($request->date_expiration));
            $service_invoice['payment_form']['duration_measure'] = $request->time_days_credit;

            $id_test = $company->test_id;
            $base_url = env("SERVICE_FACT", "");
            if($company->type_environment_id == 2)
                $ch = curl_init("{$base_url}ubl2.1/invoice/{$id_test}");
            else
                $ch = curl_init("{$base_url}ubl2.1/invoice");
            $data_document = json_encode($service_invoice);

//                        $file = fopen("C:\\DEBUG.TXT", "w");
//                        fwrite($file, json_encode(array("sendmail" => true)));
//                        fclose($file);
//            return $data_document;
//return json_encode(array("sendmail" => true));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($data_document));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));
            $response = curl_exec($ch);
            curl_close($ch);

//            return $response;

            $response_model = json_decode($response);
            $zip_key = null;
            $invoice_status_api = null;

//                        $file = fopen("C:\\DEBUG.TXT", "w");
//                        fwrite($file, json_encode($response_model));
//                        fwrite($file, " ---- ".json_encode(array_key_exists('urlinvoicepdf', $response_model)));
//                        fclose($file);

            if(array_key_exists('urlinvoicepdf', $response_model) && array_key_exists('urlinvoicexml', $response_model))
            {
                if(!is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey))
                {
                    if(is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->Success))
                    {
                        if($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->Success == 'false')
                        {
                            return [
                                'success' => false,
                                'message' => $response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->ProcessedMessage
                            ];
                        }
                    }
                }
                else
                    if(is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey))
                    {
                        $zip_key = $response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey;
                    }
            }

            //return $zip_key;

            //declaro variuable response status en null
            $response_status = null;
            //compruebo zip_key para ejecutar servicio de status document

            if($zip_key)
            {
                //espero 3 segundos para ejecutar sevcio de status document
                sleep(3);

                $ch2 = curl_init("{$base_url}ubl2.1/status/zip/{$zip_key}");
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");

                if(file_exists(storage_path('sendmail.api'))){
                    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(array("sendmail" => true)));
                }
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    "Authorization: Bearer {$company->api_token}"
                ));
                $response_status = curl_exec($ch2);
                curl_close($ch2);
                $response_status_decoded = json_decode($response_status);

                if($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->IsValid == "true")
                    $this->setStateDocument(1, $correlative_api);
                else
                {
                    if(is_array($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string))
                        $mensajeerror = implode(",", $response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string);
                    else
                        $mensajeerror = $response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string;
                    if($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->IsValid == 'false')
                    {
//                        $file = fopen("C:\\DEBUG.TXT", "w");
//                        fwrite($file, $response_status);
//                        fclose($file);

                        return [
                            'success' => false,
                            'message' => "Error al Validar Factura Nro: {$correlative_api} Errores: ".$mensajeerror
                        ];
                    }
                }
            }
            else
                return [
                    'success' => false,
                    'message' => "Error de ZipKey."
                ];

            ///-------------------------------

            $nextConsecutive = FacadeDocument::nextConsecutive($request->type_document_id);
//            return json_encode($nextConsecutive);
            $this->company = Company::query()
                ->with('country', 'version_ubl', 'type_identity_document')
                ->firstOrFail();

            if (($this->company->limit_documents != 0) && (Document::count() >= $this->company->limit_documents)) throw new \Exception("Has excedido el límite de documentos de tu cuenta.");

            $this->document = Document::create([
                'type_document_id' => $request->type_document_id,
                'prefix' => $nextConsecutive->prefix,
                'number' => $correlative_api,
                'type_invoice_id' => $request->type_invoice_id,
                'client_id' => $request->client_id,
                'client' => Client::with('typePerson', 'typeRegime', 'typeIdentityDocument', 'country', 'department', 'city')->findOrFail($request->client_id),
                'currency_id' => $request->currency_id,
                'date_issue' => Carbon::parse("{$request->date_issue} ".Carbon::now()->format('H:i:s')),
                'date_expiration' => Carbon::parse("{$request->date_expiration}"),
                'observation' => $request->observation,
                'reference_id' => $request->reference_id,
                'note_concept_id' => $request->note_concept_id,
                'sale' => $request->sale,
                'total_discount' => $request->total_discount,
                'taxes' => $request->taxes,
                'total_tax' => $request->total_tax,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'version_ubl_id' => $this->company->version_ubl_id,
                'ambient_id' => $this->company->ambient_id,

                'payment_form_id' =>$request->payment_form_id,
                'payment_method_id' =>$request->payment_method_id,
                'time_days_credit' => $request->time_days_credit,

                'response_api' => $response,
                'response_api_status' => $response_status,
                'correlative_api' => $correlative_api

            ]);

            /*$this->document->update([
                'xml' => $this->getFileName(),
                'cufe' => $this->getCufe()
            ]);*/

            foreach ($request->items as $item) {
                DetailDocument::create([
                    'document_id' => $this->document->id,
                    'item_id' => $item['id'],
                    'item' => $item,
                    'type_unit_id' => $item['type_unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_id' => $item['tax_id'],
                    'tax' => Tax::find($item['tax_id']),
                    'total_tax' => $item['total_tax'],
                    'subtotal' => $item['subtotal'],
                    'discount' => $item['discount'],
                    'total' => $item['total']
                ]);
            }
        }
        catch (\Exception $e) {
            DB::connection('tenant')->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        DB::connection('tenant')->commit();

        return [
            'success' => true,
            'message' => "Se registro con éxito el documento #{$this->document->prefix}{$nextConsecutive->number}.",
           //'data' => $data_document
        ];
    }


    public function storeNote(DocumentRequest $request) {
        DB::connection('tenant')->beginTransaction();
        try {

            // return $correlative_api;
            $note_service = $request->note_service;
            $url_name_note = '';
            $type_document_service = $note_service['type_document_id'];
            if( $type_document_service == 4)
            {
                $url_name_note = 'credit-note';
            }
            elseif($type_document_service == 5)
            {
                $url_name_note = 'debit-note';
            }

            $correlative_api = $this->getCorrelativeInvoice($type_document_service);

            if(!is_numeric($correlative_api)){
                return [
                    'success' => false,
                    'message' => 'Error al obtener correlativo Api.'
                ];
            }

            //return $correlative_api;

            $note_service['number'] = $correlative_api;
            $note_service['date'] = date('Y-m-d');
            $note_service['time'] = date('H:i:s');

            $datoscompany = Company::with('type_regime', 'type_identity_document')->firstOrFail();
            $company = ServiceTenantCompany::firstOrFail();

//            return json_encode($request->note_concept_id);

            $note_concept_id = NoteConcept::query()->where('id', $request->note_concept_id)->get();
            $note_service['discrepancyresponsecode'] = $note_concept_id[0]->code;
            $note_service['ivaresponsable'] = $datoscompany->type_regime->name;
            $note_service['nombretipodocid'] = $datoscompany->type_identity_document->name;
            $note_service['tarifaica'] = $datoscompany->ica_rate;
            $note_service['actividadeconomica'] = $datoscompany->economic_activity_code;
            $note_service['notes'] = $request->observation;
            if(file_exists(storage_path('sendmail.api'))){
                $note_service['sendmail'] = true;
            }

            $id_test = $company->test_id;
            $base_url = env("SERVICE_FACT", "");
            if($company->type_environment_id == 2)
                $ch = curl_init("{$base_url}ubl2.1/{$url_name_note}/{$id_test}");
            else
                $ch = curl_init("{$base_url}ubl2.1/{$url_name_note}");
            $data_document = json_encode($note_service);

//            return $data_document;

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($data_document));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            $response_model = json_decode($response);
            $zip_key = null;
            $invoice_status_api = null;

            if(array_key_exists('urlinvoicepdf', $response_model) && array_key_exists('urlinvoicexml', $response_model) )
            {
                if(!is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey))
                {
                    if(is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->Success))
                    {
                        if($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->Success == 'false')
                        {
                            return [
                                'success' => false,
                                'message' => $response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->XmlParamsResponseTrackId->ProcessedMessage
                            ];
                        }
                    }
                }
                else
                {
                    if(is_string($response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey))
                    {
                        $zip_key = $response_model->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey;
                    }
                }
            }

            //declaro variuable response status en null
            $response_status = null;
            //compruebo zip_key para ejecutar servicio de status document
            if($zip_key)
            {
                //espero 3 segundos para ejecutar sevcio de status document
                sleep(3);

                $ch2 = curl_init("{$base_url}ubl2.1/status/zip/{$zip_key}");
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");
                if(file_exists(storage_path('sendmail.api'))){
                    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(array("sendmail" => true)));
                }
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    "Authorization: Bearer {$company->api_token}"
                ));
                $response_status = curl_exec($ch2);
                curl_close($ch2);

                $response_status_decoded = json_decode($response_status);
                if($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->IsValid == "true")
                    $this->setStateDocument($type_document_service, $correlative_api);
                else
                {
                    if(is_array($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string))
                        $mensajeerror = implode(",", $response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string);
                    else
                        $mensajeerror = $response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->ErrorMessage->string;
                    if($response_status_decoded->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->IsValid == 'false')
                    {
                        return [
                            'success' => false,
                            'message' => "Error al Validar Factura Nro: {$correlative_api} Errores: ".$mensajeerror
                        ];
                    }
                }
            }
            else
                return [
                    'success' => false,
                    'message' => "Error de ZipKey."
                ];

            ///-------------------------------

            $nextConsecutive = FacadeDocument::nextConsecutive($request->type_document_id);

            $this->company = Company::query()
                ->with('country', 'version_ubl', 'type_identity_document')
                ->firstOrFail();

            if (($this->company->limit_documents != 0) && (Document::count() >= $this->company->limit_documents)) throw new \Exception("Has excedido el límite de documentos de tu cuenta.");

            $this->document = Document::create([
                'type_document_id' => $request->type_document_id,
                'prefix' => $nextConsecutive->prefix,
                'number' => $correlative_api,
                'type_invoice_id' => $request->type_invoice_id,
                'client_id' => $request->client_id,
                'client' => Client::with('typePerson', 'typeRegime', 'typeIdentityDocument', 'country', 'department', 'city')->findOrFail($request->client_id),
                'currency_id' => $request->currency_id,
                'date_issue' => Carbon::parse("{$request->date_issue} ".Carbon::now()->format('H:i:s')),
                'date_expiration' => Carbon::parse("{$request->date_expiration}"),
                'observation' => $request->observation,
                'reference_id' => $request->reference_id,
                'note_concept_id' => $request->note_concept_id,
                'sale' => $request->sale,
                'total_discount' => $request->total_discount,
                'taxes' => $request->taxes,
                'total_tax' => $request->total_tax,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'version_ubl_id' => $this->company->version_ubl_id,
                'ambient_id' => $this->company->ambient_id,

                'payment_form_id' =>$request->payment_form_id,
                'payment_method_id' =>$request->payment_method_id,
                'time_days_credit' => $request->time_days_credit,

                'response_api' => $response,
                'response_api_status' => $response_status,
                'correlative_api' => $correlative_api
            ]);

            $this->document->update([
                'xml' => $this->getFileName(),
                'cufe' => $this->getCufe()
            ]);

            foreach ($request->items as $item) {
                DetailDocument::create([
                    'document_id' => $this->document->id,
                    'item_id' => $item['id'],
                    'item' => $item,
                    'type_unit_id' => $item['type_unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_id' => $item['tax_id'],
                    'tax' => Tax::find($item['tax_id']),
                    'total_tax' => $item['total_tax'],
                    'subtotal' => $item['subtotal'],
                    'discount' => $item['discount'],
                    'total' => $item['total']
                ]);
            }
        }
        catch (\Exception $e) {
            DB::connection('tenant')->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        DB::connection('tenant')->commit();

        return [
            'success' => true,
            'message' => "Se registro con éxito el documento #{$this->document->prefix}{$this->document->number}.",
           //'data' => $data_document
        ];
    }

    /**
     * Download
     * @param  string   $type
     * @param  \App\Models\Tenant\Document $document
     * @return void
     */

     public function download($type, Document $document) {
        switch ($type) {
            case 'xml':

                return $this->downloadDocument($type, $document);

                break;
            case 'pdf':
                $mpdf = new Mpdf([
                    'tempDir' => storage_path('mpdf')
                ]);


                $servicecompany = TenantServiceCompany::firstOrFail();

                $mpdf->WriteHTML(view("pdf/{$document->type_document->template}", [
                    'typeIdentityDocuments' => TypeIdentityDocument::all(),
                    'company' => Company::firstOrFail(),
                    'servicecompany' => $servicecompany,
                    'document' => $document,

                ])->render());

                $mpdf->Output("{$document->prefix}{$document->number}.{$type}", 'D');
            default:
                throw new \Exception("The document does not exist", 1);

                break;
        }
    }

    public function sendEmail($number, $client)
    {
        /*$company = Company::firstOrFail();
        $document = Document::find($document);
        $client = Client::find($client);
        $servicecompany =  TenantServiceCompany::firstOrFail();
        $customer_email = $client->email;
        Mail::to($customer_email)->send(new SendGraphicRepresentation($company, $document, $servicecompany ));
        return [
            'success' => true,
            'message' => "Email enviado con éxito."
        ];*/
        $client = Client::find($client);

        $company = ServiceTenantCompany::firstOrFail();

        $send= (object)['number'=> $number, 'email'=> $client->email];
        $data_send = json_encode($send);

        $base_url = env("SERVICE_FACT", "");
        $ch2 = curl_init("{$base_url}send_mail");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch2, CURLOPT_POSTFIELDS,($data_send));
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));

        $response = curl_exec($ch2);
        $respuesta = json_decode($response);
        curl_close($ch2);

        if(property_exists($respuesta, 'success'))
        {
            return [
                'success' => $respuesta->success,
                'message' => $respuesta->message
            ];
        }
        else{

            return [
                'success' => false,
                'message' => 'No se puedo enviar el correo.'
            ];

        }
    }

    public function getCorrelativeInvoice($type_service)
    {
        $company = ServiceTenantCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");
        $ch2 = curl_init("{$base_url}ubl2.1/invoice/current_number/{$type_service}");

        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        $response_data = curl_exec($ch2);
        $err = curl_error($ch2);
        curl_close($ch2);
        $response_encode = json_decode($response_data);
        if($err){
            return null;
        }
        else{
            return $response_encode->number;
        }
    }

    public function setStateDocument($type_service, $DocumentNumber)
    {
        $company = ServiceTenantCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");
        $ch2 = curl_init("{$base_url}ubl2.1/invoice/state_document/{$type_service}/{$DocumentNumber}");

        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        $response_data = curl_exec($ch2);
        $err = curl_error($ch2);
        curl_close($ch2);
        $response_encode = json_decode($response_data);
        if($err){
            return null;
        }
        else{
            return $response_encode;
        }
    }

    public function downloadxml($id)
    {
        $invoice =  Document::find($id);

        $api = json_decode($invoice->response_api);

        $data = base64_decode($api->invoicexml);

        $correlativo = $invoice->correlative_api;
        /*$file = fopen( storage_path('app')."/invoice/invoice-{$correlativo}.xml", "w");
        fwrite($file, $data . PHP_EOL);
        fclose($file);*/

        Storage::disk('tenant')->put("invoice_download/invoice-{$correlativo}.xml", $data );
        return Storage::disk('tenant')->download("invoice_download/invoice-{$correlativo}.xml");

       // return response()->download(storage_path("app/invoice/invoice-{$correlativo}.xml"));

    }
}
