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
    ReceivedDocument
};
use Modules\RadianEvent\Http\Resources\{
    ReceivedDocumentCollection
};
use Modules\RadianEvent\Helpers\ZipHelper;


class SearchEmailController extends Controller
{
    
    // public function reception()
    // {
    //     return view('radianevent::reception.index');
    // }

    
    public function searchImapEmails()
    {

        $advanced_configuration = AdvancedConfiguration::selectImapColumns()->firstOrFail();
        
        $imap_server = '{'.$advanced_configuration->radian_imap_host.':'.$advanced_configuration->radian_imap_port.'/imap/'.$advanced_configuration->radian_imap_encryption.'}INBOX';
        
        $mailbox = new \PhpImap\Mailbox($imap_server, $advanced_configuration->radian_imap_user, $advanced_configuration->radian_imap_password);
        
        try 
        {
            $all_mails_id = $mailbox->searchMailbox('FROM alexander_obando@hotmail.com');
            // $all_mails_id = $mailbox->searchMailbox('ALL');
        }
        catch(PhpImap\Exceptions\ConnectionException $ex) 
        {
            return $this->getGeneralResponse(false, 'Conexión IMAP fallida: ' . implode(",", $ex->getErrors('all')));
        }

        if(!$all_mails_id) 
        {
            return $this->getGeneralResponse(false, 'No se encontraron correos.');
        }
        
        // Get the first message
        // If '__DIR__' was defined in the first line, it will automatically
        // save all attachments to the specified directory
        // dd($mailbox, $all_mails_id);

        $selected_emails = [];

        foreach ($all_mails_id as $key => $mail_id) 
        {
            $mail = $mailbox->getMail($mail_id);
            // dd($mail,$mail->hasAttachments() , count($all_mails_id), $mail->getAttachments());

            // validar si el correo cumple las condiciones
            if($this->isValidEmail($mail))
            {

                $attachment = collect($mail->getAttachments())->first();
                $attachment_content = $attachment->getContents(); //zip

                $folder = "emails_radian_reception_documents";
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
                        // aqui ya tengo ambos archivos tengo que enviarlos a la api, para que valide si son aptos para insertar en received documents
                        
                        // tengo que verificar primero, si ya existe en mi bd local

                        // if(Storage::disk('tenant')->exists($folder.DIRECTORY_SEPARATOR.$filename)) throw new Exception('El archivo ya fue cargado');

                        // // enviar api para parsear xml y obtener data
                        // $company = ServiceCompany::select('identification_number', 'api_token')->firstOrFail();
                        // $connection_api = new HttpConnectionApi($company->api_token);
                        
                        // $params = [
                        //     'xml_document' => base64_encode($file_content),
                        //     'company_idnumber' => $company->identification_number,
                        // ];

                        // $url = "process-seller-document-reception";
                        // $send_request_to_api = $connection_api->sendRequestToApi($url, $params, 'POST');

                        // if(!$send_request_to_api['success']) throw new Exception($send_request_to_api['message']);
                        // // enviar api


                        // //subir archivo 
                        // Storage::disk('tenant')->put($folder.DIRECTORY_SEPARATOR.$filename, $file_content);

                        // // registrar en bd
                        // $data = $send_request_to_api['data'];
                        // $data['xml'] = $filename;
                        // $data['pdf'] = $filename_pdf;
                        
                        // ReceivedDocument::create($data);

                        // return [
                        //     'success' => true,
                        //     'message' =>  'Archivo cargado',
                        //     'send_request_to_api' => $send_request_to_api
                        // ];
                    }
                }

                dd($extract_zip);

                Storage::disk('tenant')->put($folder.DIRECTORY_SEPARATOR.$filename, $attachment_content);

                $selected_emails [] = [
                    'subject' => $mail->subject,
                    'from_host' => $mail->fromHost,
                    'from_name' => $mail->fromName,
                    'from_address' => $mail->fromAddress,
                    'sender_host' => $mail->senderHost,
                    'sender_name' => $mail->senderName,
                    'sender_address' => $mail->senderAddress,
                    'attachment' => $attachment,
                    'attachment_content' => $attachment_content,
                    'mail' => $mail,
                ];
            }
        }

        dd($selected_emails);


    }


    public function isValidEmail($mail)
    {
        $subject = $mail->subject;
        $parse_subject = explode(';',  $subject);
        $quantity_items = count($parse_subject);

        if($quantity_items > 0)
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

        return false;
    }


}
