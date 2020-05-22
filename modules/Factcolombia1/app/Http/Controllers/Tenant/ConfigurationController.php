<?php

namespace App\Http\Controllers\Tenant;

use App\Traits\Tenant\DocumentTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\{
    ConfigurationTypeDocumentRequest,
    ConfigurationUploadLogoRequest,
    ConfigurationCompanyRequest,
    ConfigurationServiceCompanyRequest,
    ConfigurationServiceSoftwareCompanyRequest,
    ConfigurationServiceCertificateCompanyRequest,
    ConfigurationServiceResolutionCompanyRequest

};
use Illuminate\Http\Request;
use App\Configuration;
use DB;
use App\Models\Tenant\{
    TypeIdentityDocument,
    TypeObligation,
    TypeDocument,
    NoteConcept,
    TypeRegime,
    VersionUbl,
    Department,
    Currency,
    Ambient,
    Company,
    Country,
    City
};
use App\Models\TenantService\{
    Company as ServiceCompany
};
use Carbon\Carbon;
use App\Models\TenantService\{
    Company as ServiceTenantCompany
};

class ConfigurationController extends Controller
{
    use DocumentTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('configuration.tenant.index');
    }
    
    public function document() {
        return view('configuration.tenant.documents');
    }
    
    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return [
            'typeIdentityDocuments' => TypeIdentityDocument::all(),
            'typeObligations' => TypeObligation::all(),
            'typeDocuments' => TypeDocument::all(),
            'typeRegimes' => TypeRegime::all(),
            'versionUbls' => VersionUbl::all(),
            'currencies' => Currency::all(),
            'countries' => Country::all(),
            'ambients' => Ambient::all()
        ];
    }
    
    /**
     * Company
     * @return \Illuminate\Http\Response
     */
    public function company() {
        /*$company = Company::query()
            ->with('currency')
            ->firstOrFail();*/
        $company = ServiceCompany::first();
        $company->alert_certificate = Carbon::parse($company->certificate_date_end)->subMonth(1)->lt(Carbon::now());
        
        $company['resolution_date'] = date("Y-m-d");
        $company['date_from'] =date("Y-m-d");
        $company['date_to'] = date("Y-m-d", strtotime("+2 days"));

        return $company;
    }
    
    /**
     * Countries
     * @return \Illuminate\Http\Response
     */
    public function countries() {
        return Country::all();
    }

    /**
     * Departments
     * @param  \App\Models\Tenant\Country $country
     * @return \Illuminate\Http\Response
     */
    public function departments(Country $country) {
        return Department::query()
            ->where('country_id', $country->id)
            ->get();
    }
    
    /**
     * Cities
     * @param  \App\Models\Tenant\Department $department
     * @return \Illuminate\Http\Response
     */
    public function cities(Department $department) {
        return City::query()
            ->where('department_id', $department->id)
            ->get();
    }
    
    /**
     * Concepts
     * @param  \App\Models\Tenant\TypeDocument $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function concepts(TypeDocument $typeDocument) {
        return NoteConcept::query()
            ->where('type_document_id', $typeDocument->id)
            ->get();
    }
    
    /**
     * Update company
     * @param  \App\Http\Requests\Tenant\ConfigurationCompanyRequest $request
     * @param  \App\Models\Tenant\Company                     $company
     * @return \Illuminate\Http\Response
     */
    public function updateCompany(ConfigurationCompanyRequest $request, Company $company) {
        if ($request->hasFile('certificate')) $this->uploadCertificate($request->certificate);
        
        $company->update([
            'type_identity_document_id' => $request->type_identity_document_id,
            'short_name' => $request->short_name,
            'email' => $request->email,
          //  'country_id' => $request->country_id,
            'department_id' => $request->department_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'phone' => $request->phone,
            //'currency_id' => $request->currency_id,
            'type_regime_id' => $request->type_regime_id,
           // 'economic_activity_code' => $request->economic_activity_code,
            'ica_rate' => $request->ica_rate,
            'type_obligation_id' => $request->type_obligation_id,
            'version_ubl_id' => $request->version_ubl_id,
            'ambient_id' => $request->ambient_id,
            'software_identifier' => $request->software_identifier,
            'software_password' => $request->software_password,
            'pin' => $request->pin,
            'certificate_name' => $request->certificate_name,
            'certificate_password' => $request->certificate_password,
            'certificate_date_end' => $request->certificate_date_end
        ]);
        
        return [
            'success' => true
        ];
    }
    
    /**
     * Update type document
     * @param  \App\Http\Requests\Tenant\ConfigurationTypeDocumentRequest $request
     * @param  \App\Models\Tenant\TypeDocument                     $typeDocument
     * @return \Illuminate\Http\Response
     */
    public function updateTypeDocument(ConfigurationTypeDocumentRequest $request, TypeDocument $typeDocument) {
        $typeDocument->update([
            'resolution_number' => $request->resolution_number,
            'resolution_date' => $request->resolution_date,
            'resolution_date_end' => $request->resolution_date_end,
            'technical_key' => $request->technical_key,
            'prefix' => mb_strtoupper($request->prefix),
            'from' => $request->from,
            'to' => $request->to
        ]);
        
        return [
            'success' => true
        ];
    }
    
    /**
     * Upload logo
     * @param  \App\Http\Requests\Tenant\ConfigurationUploadLogoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function uploadLogo(ConfigurationUploadLogoRequest $request) {
        $base_url = env("SERVICE_FACT", "");
        
        $company = Company::firstOrFail();
        $servicecompany = ServiceCompany::firstOrFail();
        $file = $request->file('file');
        
        $name = "logo_.{$company->identification_number}.{$file->getClientOriginalExtension()}";
        
        $file->storeAs('public/uploads/logos', $name);
        
        $company->logo = $name;
        $company->save();

        //--------send logo------------------

//        $file = fopen("C:\\DEBUG.txt", "w");
//        fwrite($file, storage_path('app/public/uploads/logos/'.$name));
//        fwrite($file, base64_encode(file_get_contents(storage_path('app/public/uploads/logos/'.$name))));
//        fclose($file);        

        $ch = curl_init("{$base_url}ubl2.1/config/logo");
        $data = [
            "logo"=> base64_encode(file_get_contents(storage_path('app/public/uploads/logos/'.$name))),
        ];
        $data_logo = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,($data_logo));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$servicecompany->api_token}"
        ));

        $response_logo = curl_exec($ch);
        $err = curl_error($ch);
        $respuesta = json_decode($response_logo);

        if($err)
        {   
            return [
                'message' => "Error en peticion Api.",
                'success' => false,
                'logo' => ''
            ];
        }
        else{

            if(property_exists($respuesta, 'success'))
            {
                return [
                    'message' => "Se guardaron los cambios.",
                    'success' => true,
                    'logo' => $response_logo
                ];
            }
            else{
                return [
                    'message' => "Error en validacion de datos Api.",
                    'success' => false,
                    'respuesta' => $respuesta,
                    'data' => $data_logo
                ];
            }
        }
    }

    public function storeServiceCompanie(ConfigurationServiceCompanyRequest $request)
    {
        $company = ServiceCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");

        //----send software----
        $ch = curl_init("{$base_url}ubl2.1/config/software");
        $data = [
            "id"=> $request->id_software,
            "pin"=> $request->pin_software,
            "url" => $request->url_software,
        ];
        $data_software = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,($data_software));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        $response_software = curl_exec($ch);
        $company->response_software = $response_software;
      
        //----------------------

        //--------send cerificate------------------

        $ch2 = curl_init("{$base_url}ubl2.1/config/certificate");
        $data = [
            "certificate"=> $request->certificate64,
            "password"=>  $request->password_certificate//"Nqx4FAZ6kD"//$request->password
        ];
        $data_certificate = json_encode($data);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch2, CURLOPT_POSTFIELDS,($data_certificate));
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));

        $response_certificate = curl_exec($ch2);
        $company->response_certificate = $response_certificate;

        //------------------------------------------

        //----send resolution--------
        $ch3 = curl_init("{$base_url}ubl2.1/config/resolution");
        $data = [
            "type_document_id"=> $request->type_document_id['id'],
            "prefix"=> $request->prefix,
            "resolution"=> $request->resolution,
            "resolution_date"=> $request->resolution_date,
            "technical_key"=> $request->technical_key,
            "from"=> $request->from,
            "to"=> $request->to,
            'date_from' => $request->date_from, 
            'date_to' => $request->date_to
        ];
        $data_resolution = json_encode($data);
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch3, CURLOPT_POSTFIELDS,($data_resolution));
        curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));


        $response_resolution = curl_exec($ch3);
        $company->response_resolution = $response_resolution;

        $company->save();


        return [
            'message' => "Se guardaron los cambios.",
            'success' => true,
            //'software' => $response_software,
            'cetificate' => $response_certificate,
            //'resolution' => $response_resolution
        ];
    }


    public function storeServiceSoftware(ConfigurationServiceSoftwareCompanyRequest $request)
    {
        $company = ServiceCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");
        $ch = curl_init("{$base_url}ubl2.1/config/software");
        $data = [
            "id"=> $request->id_software,
            "pin"=> $request->pin_software,
            //"url" => $request->url_software,
        ];
        $data_software = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,($data_software));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        $response_software = curl_exec($ch);
        $err = curl_error($ch);
        $respuesta = json_decode($response_software);

        if($err)
        {   
            return [
                'message' => "Error en peticion Api.",
                'success' => false,
                'software' => ''
            ];
        }
        else{

            if(property_exists( $respuesta, 'success'))
            {
                $company->response_software = $response_software;
                $company->test_id = $request->test_id;
                $company->save();
                return [
                    'message' => "Se guardaron los cambios.",
                    'success' => true,
                    'software' => $response_software
                ];
            }
            else{

                return [
                    'message' => "Error en validacion de datos Api.",
                    'success' => false,
                    'software' => ''
                ];
            }


        }

    }

    public function storeServiceCertificate(ConfigurationServiceCertificateCompanyRequest $request)
    {
        $company = ServiceCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");

        $ch2 = curl_init("{$base_url}ubl2.1/config/certificate");
        $data = [
            "certificate"=> $request->certificate64,
            "password"=>  $request->password_certificate//"Nqx4FAZ6kD"//$request->password
        ];
        $data_certificate = json_encode($data);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch2, CURLOPT_POSTFIELDS,($data_certificate));
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        
        $response_certificate = curl_exec($ch2);
        $err = curl_error($ch2);
        $respuesta = json_decode($response_certificate);

        if($err)
        {   
            return [
                'message' => "Error en peticion Api.",
                'success' => false,
                'certificate' => ''
            ];
        }
        else{

            if(property_exists($respuesta, 'success'))
            {
                $company->response_certificate = $response_certificate;
                $company->save();
                return [
                    'message' => "Se guardaron los cambios.",
                    'success' => true,
                    'certificate' => $response_certificate
                ];
            }
            else{

                return [
                    'message' => "Error en validacion de datos Api.",
                    'success' => false,
                    'respuesta' => $respuesta,
                    'data' => $data_certificate
                ];
            }
        }
    }

    public function changeEnvironment($ambiente)
    {
        $company = ServiceCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");

        $ch2 = curl_init("{$base_url}ubl2.1/config/environment");
        if ($ambiente == 'HABILITACION')
            $data = [
                "type_environment_id" => 2,
            ];
        else    
            $data = [
                "type_environment_id" => 1,
            ];

        $data_environment = json_encode($data);

        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch2, CURLOPT_POSTFIELDS,($data_environment));
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer {$company->api_token}"
        ));
        
        $response_environment = curl_exec($ch2);
        $err = curl_error($ch2);
        $respuesta = json_decode($response_environment);

        if($err)
        {   
            return [
                'message' => "Error en peticion Api.",
                'success' => false,
            ];
        }
        else{
            if(property_exists($respuesta, 'company'))
            {
                if ($ambiente == 'HABILITACION')
                    $company->type_environment_id = 2;
                else
                    $company->type_environment_id = 1;
                $company->update();
                return [
                    'message' => "Se guardaron los cambios.",
                    'success' => true,
                ];
            }
            else{
                return [
                    'message' => "Error en validacion de datos Api.",
                    'success' => false,
                    'respuesta' => $respuesta,
                    'data' => $data_environment
                ];
            }
        }
    }

    public function storeServiceResolution(ConfigurationServiceResolutionCompanyRequest $request)
    {
        try{
            $company = ServiceCompany::firstOrFail();
            $base_url = env("SERVICE_FACT", "");

            $ch3 = curl_init("{$base_url}ubl2.1/config/resolution");
            $data = [
                "type_document_id"=> $request->type_document_id['id'],
                "prefix"=> $request->prefix,
                "resolution"=> $request->resolution,
                "resolution_date"=> $request->resolution_date,
                "technical_key"=> $request->technical_key,
                "from"=> $request->from,
                "to"=> $request->to,
                'date_from' => $request->date_from, 
                'date_to' => $request->date_to
            ];
            $data_resolution = json_encode($data);
            curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch3, CURLOPT_POSTFIELDS,($data_resolution));
            curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            $response_resolution = curl_exec($ch3);
            $err = curl_error($ch3);
            curl_close($ch3);
            $respuesta = json_decode($response_resolution);

            if($err)
            {   
                return [
                    'message' => "Error en peticion Api Resolution.",
                    'success' => false,
                    'resolution' => ''
                ];
            }
        

            if(property_exists( $respuesta, 'success'))
            {
                $company->response_resolution = $response_resolution;
                $company->save();

                TypeDocument::updateOrCreate([
                    'code' => 1
                ], [
                    'resolution_number' => $request->resolution,
                    'resolution_date' => $request->date_from,
                    'resolution_date_end' => $request->date_to,
                    'prefix' => $request->prefix,
                    'technical_key' => $request->technical_key,
                    'from' => $request->from,
                    'to' => $request->to
                ]);
    
                $this->storeResolutionNote();

//                $file = fopen("C:\\DEBUG.TXT", "w");
//                fwrite($file, $request->prefix.PHP_EOL);
//                fclose($file);        

                if ($request->prefix == 'SETP')
                    $this->changeEnvironment('HABILITACION');
                else 
                    $this->changeEnvironment('PRODUCCION');

                return [
                    'message' => "Se guardaron los cambios.",
                    'success' => true,
                    'resolution' => $response_resolution
                ];
        }
        else{

            return [
                'message' => "Error en validacion de datos Api.",
                'success' => false,
                'resolution' => $response_resolution
            ];
        }
        }
        catch(\Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }        
    }


    //verifica si la configracion esta completa, ejecuta el test : 60 facturas,  20 notas credito, 20 notas debito
    public function testApiDian()
    {
        $company = ServiceTenantCompany::firstOrFail();
        $base_url = env("SERVICE_FACT", "");
        $id_test = $company->test_id;

        //envio 60 facturas
        $json_invoice = '{"number":994688605,"type_document_id":1,"customer":{"identification_number":"323232323","name":"peres","phone":"3232323","address":"sdsdsdsdsd","email":"peres@mail.com","merchant_registration":"No tiene"},"tax_totals":[{"tax_id":1,"percent":"19.00","tax_amount":"57000.00","taxable_amount":"300000.00"}],"legal_monetary_totals":{"line_extension_amount":"300000.00","tax_exclusive_amount":"300000.00","tax_inclusive_amount":"357000.00","allowance_total_amount":"0.00","charge_total_amount":"0.00","payable_amount":"357000.00"},"invoice_lines":[{"unit_measure_id":642,"invoiced_quantity":"1","line_extension_amount":"300000.00","free_of_charge_indicator":false,"tax_totals":[{"tax_id":1,"tax_amount":"57000.00","taxable_amount":"300000.00","percent":"19.00"}],"description":"POLO","code":"2323","type_item_identification_id":3,"price_amount":"13.09","base_quantity":"1.000000"}]}';
        $response_invoice = array();
       // for ($i=1; $i <=60 ; $i++) { 
            $ch = curl_init("{$base_url}ubl2.1/invoice/{$id_test}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($json_invoice));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            $response = curl_exec($ch);
            array_push($response_invoice, $response);
            
       // }

        /*//envio 20 notas de credito
        $json_credit_note = '';
        $response_credit_note = array();
        for ($i=1; $i <=20 ; $i++) { 
            $ch = curl_init("{$base_url}ubl2.1/credit-note/{$id_test}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($json_credit_note));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            $response = curl_exec($ch);
            array_push($response_credit_note, $response);
            
        }

        
        //envio 20 notas de debito
        $json_debit_note = '';
        $response_debit_note = array();
        for ($i=1; $i <=20 ; $i++) { 
            $ch = curl_init("{$base_url}ubl2.1/debit-note/{$id_test}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,($json_debit_note));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            $response = curl_exec($ch);
            array_push($response_debit_note, $response);
            
        }*/


        return [
            '60_times_invoice' =>  $response_invoice,
            //'60_times_credit_note' =>  $response_credit_note,
            //'60_times_debit_note' =>  $response_debit_note,
        ];

    }

    public function storeResolutionNote()
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $company = ServiceCompany::firstOrFail();
            $base_url = env("SERVICE_FACT", "");
            //NOTA CREDITO
            $ch5 = curl_init("{$base_url}ubl2.1/config/resolution");
            $data_c = [
                "type_document_id"=> 4,
                "from"=> 1,
                "to"=> 99999999,
                "prefix"=> "NC",
            ];

            $data_resolution = json_encode($data_c);
            curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch5, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch5, CURLOPT_POSTFIELDS,($data_resolution));
            curl_setopt($ch5, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            $response_credit = curl_exec($ch5);
            curl_close($ch5);
            $company->response_resolution_credit = $response_credit;

            TypeDocument::updateOrCreate([
                'code' => 3
            ], [
                'resolution_date' => NULL,
                'resolution_date_end' => NULL,
                'prefix' => "NC",
                'from' => 1,
                'to' => 99999999
            ]);

            //NOTA DEBITO
            $ch4 = curl_init("{$base_url}ubl2.1/config/resolution");
            $data_d = [
                "type_document_id"=> 5,
                "from"=> 1,
                "to"=> 99999999,
                "prefix"=> "ND",
            ];
            $data_resolution_de = json_encode($data_d);
            curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch4, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch4, CURLOPT_POSTFIELDS,($data_resolution_de));
            curl_setopt($ch4, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                "Authorization: Bearer {$company->api_token}"
            ));

            TypeDocument::updateOrCreate([
                'code' => 2
            ], [
                'resolution_date' => NULL,
                'resolution_date_end' => NULL,
                'prefix' => "ND",
                'from' => 1,
                'to' => 99999999
            ]);

            $response_debit = curl_exec($ch4);
            curl_close($ch4);
            $company->response_resolution_debit = $response_debit;
            $company->save();
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
            'message' => "Se registraron con éxito las resoluciones para notas contables.",
        ];

    }
}