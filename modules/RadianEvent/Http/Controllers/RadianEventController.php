<?php

namespace Modules\RadianEvent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Factcolombia1\Helpers\HttpConnectionApi;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceCompany
};


class RadianEventController extends Controller
{
    
    public function reception()
    {
        return view('radianevent::radian_events.reception');
    }

    
    public function upload(Request $request)
    {
        if ($request->hasFile('file'))
        {
            try {
                // dd($request->file('file'));
                $file = $request->file('file');

                $file_content = file_get_contents($file);

                Storage::disk('tenant')->put("radian_reception_documents".DIRECTORY_SEPARATOR.$file->getClientOriginalName(), $file_content);

                // enviar api
                $company = ServiceCompany::select('api_token')->firstOrFail();
                $connection_api = new HttpConnectionApi($company->api_token);
                
                $params = [
                    'xml_document' => base64_encode(Storage::disk('tenant')->get("radian_reception_documents".DIRECTORY_SEPARATOR.$file->getClientOriginalName())),
                    'company_idnumber' => "900166483",
                ];

                $url = "process-seller-document-reception";
                // dd($url, $params);
                
                $send_request_to_api = $connection_api->sendRequestToApi($url, $params, 'POST');


                return [
                    'success' => true,
                    'message' =>  'Archivo cargado',
                    'send_request_to_api' => $send_request_to_api
                ];
                
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' =>  $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

}
