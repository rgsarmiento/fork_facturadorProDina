<?php

namespace Modules\Purchase\Helpers;

use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use Illuminate\Support\Str;
use Modules\Factcolombia1\Helpers\HttpConnectionApi;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceCompany
};
use App\Models\Tenant\{
    Person
};
use Exception;

class SupportDocumentHelper
{

    const REGISTERED = 1;
    const ACCEPTED = 5;
    const REJECTED = 6;

    private $company;

    public function __construct()
    {
        $this->company = ServiceCompany::select('api_token', 'type_environment_id')->firstOrFail();
    }
    
    /**
     * Retorna arreglo con data lista para insertar en payroll
     *
     * @param  mixed $inputs
     * @return array
     */
    public function getInputs($inputs)
    {
        $establishment_id = auth()->user()->establishment_id;
        $establishment = EstablishmentInput::set($establishment_id);
        $number = $this->getNumber($inputs->resolution_code, $inputs->prefix);

        $values = [
            'number' => $number,
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => $establishment_id,
            'establishment' => $establishment,
            'supplier' => Person::with('typePerson', 'typeRegime', 'identity_document_type', 'country', 'department', 'city')->findOrFail($inputs->supplier_id),
            'state_document_id' => self::REGISTERED, //estado inicial
            'type_environment_id' => $this->company->type_environment_id,
        ];
 
        return $inputs->merge($values)->all();
    }

    
    public function sendToApi($support_document, $inputs)
    {
        $connection_api = new HttpConnectionApi($this->company->api_token);
        
        $params = $this->getParamsForApi($support_document, $inputs);
        $url = "ubl2.1/support-document";
        // dd($url, $params);
        
        $send_request_to_api = $connection_api->sendRequestToApi($url, $params, 'POST');

        //error validacion form request api
        if(isset($send_request_to_api['errors']))
        {
            $message = $connection_api->parseErrorsToString($send_request_to_api['errors']);
            $this->throwException($message);
        }

        // dd($send_request_to_api);

        // validacion respuesta api entorno Pruebas/Produccion
        $this->validateResponseApi($send_request_to_api, $support_document->number_full, $connection_api, $support_document);

        return $send_request_to_api;

    }

    
    private function validateResponseApi($send_request_to_api, $number_full, HttpConnectionApi $connection_api, $support_document)
    {
        //TODO parsear respuesta y verificar
        $send_bill_sync_result = $send_request_to_api['ResponseDian']['Envelope']['Body']['SendBillSyncResponse']['SendBillSyncResult'];

        if($send_bill_sync_result['IsValid'] == 'true')
        {
            //estado aceptado en produccion, deberia actualizar un campo en bd para mostrar el mensaje directo de la dian
            $this->updateStateDocument(self::ACCEPTED, $support_document);
        }
        else
        {
            // estado rechazado
            $extract_error_response = $send_bill_sync_result['ErrorMessage']['string'] ?? $send_bill_sync_result['StatusDescription'];
            $error_message_response = is_array($extract_error_response) ?  implode(",", $extract_error_response) : $extract_error_response;
            $this->throwException("Error al Validar Documento de soporte Nro: {$number_full} Errores: {$error_message_response}");

        }
    }
    

    public function updateStateDocument($state_document_id, $support_document)
    {
        $support_document->update([
            'state_document_id' => $state_document_id
        ]);
    }

    
    public function getParamsForApi($support_document, $inputs)
    {
        $form_api = $inputs['data_api'];
        $form_api['number'] = $support_document->number;
        // dd($form_api, $support_document, $inputs);

        return $form_api;
    }

    
    public function throwException($message)
    {
        throw new Exception($message);
    }

    
    /**
     * Obtener correlativo desde el api
     *
     * @param  mixed $type_service
     * @param  mixed $ignore_state_document_id
     * @param  mixed $prefix
     */
    public function getNumber($type_service, $prefix)
    {
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $url = "ubl2.1/invoice/current_number/{$type_service}/{$prefix}";

        $send_request_to_api = $connection_api->get($url);

        if(isset($send_request_to_api['success']))
        {
            return $send_request_to_api['number'];
        }

        return null;
    }

}