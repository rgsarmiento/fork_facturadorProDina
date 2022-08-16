<?php

namespace Modules\RadianEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Factcolombia1\Helpers\HttpConnectionApi;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceCompany,
    AdvancedConfiguration,
    TypeDocument
};
use Exception;
use Modules\RadianEvent\Models\{
    ReceivedDocument,
    EmailReadingDetail,
    EmailReading,
};
use Modules\RadianEvent\Http\Resources\{
    ReceivedDocumentCollection
};
use Modules\RadianEvent\Helpers\ZipHelper;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Traits\UtilityTrait; 


class SearchEmailController extends Controller
{
    
    use UtilityTrait;

    // public function reception()
    // {
    //     return view('radianevent::reception.index');
    // }

    private function getEmails()
    {
        $advanced_configuration = AdvancedConfiguration::selectImapColumns()->firstOrFail();

        $imap_server = '{'.$advanced_configuration->radian_imap_host.':'.$advanced_configuration->radian_imap_port.'/imap/'.$advanced_configuration->radian_imap_encryption.'}INBOX';
        
        $mailbox = new \PhpImap\Mailbox($imap_server, $advanced_configuration->radian_imap_user, $advanced_configuration->radian_imap_password);
        
        try 
        {
            // $all_emails_id = $mailbox->searchMailbox('FROM alexander_obando@hotmail.com');
            $all_emails_id = $mailbox->searchMailbox('ALL');

            if(!$all_emails_id) return $this->getGeneralResponse(false, 'No se encontraron correos.');

            return [
                'success' => true,
                'imap_server' => $imap_server,
                'radian_imap_user' => $advanced_configuration->radian_imap_user,
                'emails' => $all_emails_id,
                'mailbox' => $mailbox,
            ];

        }
        catch(PhpImap\Exceptions\ConnectionException $ex) 
        {
            return $this->getGeneralResponse(false, 'Conexión IMAP fallida: ' . implode(",", $ex->getErrors('all')));
        }

    }
    
    public function searchImapEmails()
    {

        $emails = $this->getEmails();

        if(!$emails['success']) return $emails;

        $email_reading = EmailReading::create([
            'email_user' => $emails['radian_imap_user'],
            'start_date' => date('Y-m-d'),
            'start_time' => date('H:i:s'),
            'imap_server' => $emails['imap_server'],
        ]);
        
        try 
        {
            $data = DB::connection('tenant')->transaction(function() use($emails, $email_reading) {
            
                $all_emails_id = $emails['emails'];
                $mailbox = $emails['mailbox'];
                
                $selected_emails = [];
                $xxxx = [];
                
        
                foreach ($all_emails_id as $key => $email_id) 
                {
                    $mail = $mailbox->getMail($email_id);
        
                    // validar si el correo cumple las condiciones
                    $data_upload = null;
        
                    if($this->isValidEmail($mail, $email_reading))
                    {
                        // obtener archivos del correo
                        $attachment = collect($mail->getAttachments())->first();
                        $attachment_content = $attachment->getContents(); //zip
        
                        $filename = $attachment->name;
        
                        $extract_zip = (new ZipHelper())->extractZip($attachment_content);
        
                        if(count($extract_zip) === 2) // se valida si tiene 2 archivos, xml y pdf
                        {
                            $xml_filename = $extract_zip[0]['filename'];
                            $xml_content = $extract_zip[0]['content'];
            
                            $pdf_filename = $extract_zip[1]['filename'];
                            $pdf_content = $extract_zip[1]['content'];
        
                            if(str_contains($xml_filename, '.xml') && str_contains($pdf_filename, '.pdf'))
                            {
                                // verificar si existe el xml
                                $exist_received_document = ReceivedDocument::select('id')->where('xml', $xml_filename)->first();
        
                                if(!$exist_received_document)
                                {
                                    // enviar api para parsear xml y obtener data
                                    $send_request_to_api = $this->sendXmlToApi($xml_content);
        
                                    // registrar en bd
                                    $email_reading_detail = $this->saveEmailReadingDetail($email_reading, $mail);
                                    
                                    if($send_request_to_api['success'])
                                    {
                                        $data = $send_request_to_api['data'];
                                        $data['xml'] = $xml_filename;
                                        $data['pdf'] = $pdf_filename;
                                        
                                        $this->updateEmailReadingDetail($email_reading_detail, [
                                            'success' => true,
                                            'response_api' => $data
                                        ]);

                                        //insertar detalle
                                        $email_reading_detail->received_document()->create($data);

                                        //subir archivo 
                                        $this->uploadFile($pdf_filename, $pdf_content);
                                        $this->uploadFile($xml_filename, $xml_content);

                
                                        // $data_upload = [
                                        //     'success' => true,
                                        //     'response_api' => $data
                                        // ];
                                    }
                                    else
                                    {
                                        $this->updateEmailReadingDetail($email_reading_detail, [
                                            'success' => false,
                                            'response_api' => $send_request_to_api
                                        ]);

                                        // $data_upload = [
                                        //     'success' => false,
                                        //     'response_api' => $send_request_to_api
                                        // ];
                                    }
                                }
                            }
                        }
        
                        // dd($extract_zip);
                        // $selected_emails [] = [
                        //     'subject' => $mail->subject,
                        //     'from_host' => $mail->fromHost,
                        //     'from_name' => $mail->fromName,
                        //     'from_address' => $mail->fromAddress,
                        //     'sender_host' => $mail->senderHost,
                        //     'sender_name' => $mail->senderName,
                        //     'sender_address' => $mail->senderAddress,
                        //     'data_upload' => $data_upload,
                        //     // 'attachment' => $attachment,
                        //     // 'attachment_content' => $attachment_content,
                        //     'mail' => $mail,
                        // ];
        
                        // $xxxx [] = [
                        //     'data_upload' => $data_upload,
                        // ];
                    }
                }

                $email_reading->update([
                    'end_date'=> date('Y-m-d'),
                    'end_time'=> date('H:i:s'),
                    'success'=> true,
                ]);

                return $this->getGeneralResponse(true, "Proceso realizado correctamente: {$email_reading->details()->count()} correos fueron registrados.");
        
                // dd($xxxx, $selected_emails);
                
                // return $document;

            });

            return $data;
        } 
        catch (Exception $e) 
        {
            $email_reading->errors = $e->getMessage();
            return $this->getErrorFromException($e->getMessage(), $e);
        }

    }


    private function updateEmailReadingDetail(&$email_reading_detail, $data)
    {
        $email_reading_detail->api_validation_response = $data;
        $email_reading_detail->save();
    }


    private function saveEmailReadingDetail($email_reading, $mail)
    {
        return $email_reading->details()->create([
            'email_user' => $email_reading->email_user,
            'email_id' => $mail->id,
            'subject' => $mail->subject,
            'from_host' => $mail->fromHost,
            'from_name' => $mail->fromName,
            'from_address' => $mail->fromAddress,
            'sender_host' => $mail->senderHost,
        ]);
    }


    private function uploadFile($filename, $content, $folder = 'radian_reception_documents')
    {
        Storage::disk('tenant')->put($folder.DIRECTORY_SEPARATOR.$filename, $content);
    }


    private function sendXmlToApi($xml_content)
    {
        $company = ServiceCompany::select('identification_number', 'api_token')->firstOrFail();
        $connection_api = new HttpConnectionApi($company->api_token);
        
        $params = [
            'xml_document' => base64_encode($xml_content),
            'company_idnumber' => $company->identification_number,
        ];

        return $connection_api->sendRequestToApi('process-seller-document-reception', $params, 'POST');
    }


    public function isValidEmail($mail, $email_reading)
    {
        $subject = $mail->subject;
        $parse_subject = explode(';',  $subject);
        $quantity_items = count($parse_subject);

        $email_reading_detail = EmailReadingDetail::where('email_user', $email_reading->email_user)
                                                    ->where('email_id', $mail->id)
                                                    ->select('id')
                                                    ->first();

        // validar si es que no existe el email registrado
        if($quantity_items > 0 && !$email_reading_detail)
        {
            if(isset($parse_subject[3]))
            {
                $type_document_code = trim($parse_subject[3]);
                $exist_type_document = TypeDocument::where('code', $type_document_code)->select('id')->first();
    
                if($quantity_items === 5 && is_numeric($parse_subject[0]) && $exist_type_document && $mail->hasAttachments())
                {
                    if(count($mail->getAttachments()) === 1)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }


}
