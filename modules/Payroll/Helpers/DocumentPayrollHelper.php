<?php

namespace Modules\Payroll\Helpers;

use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use Illuminate\Support\Str;
use Modules\Factcolombia1\Helpers\HttpConnectionApi;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceCompany
};
use Modules\Payroll\Models\{
    DocumentPayroll,
};
use Exception;

class DocumentPayrollHelper
{

    const REGISTERED = 1;
    const ACCEPTED = 5;
    const REJECTED = 6;

    private $company;

    public function __construct()
    {
        $this->company = ServiceCompany::select('test_set_id_payroll', 'api_token', 'type_environment_id')->firstOrFail();
    }
    
    /**
     * Retorna arreglo con data lista para insertar en payroll
     *
     * @param  mixed $inputs
     * @return array
     */
    public function getInputs($inputs)
    {
        $establishment = EstablishmentInput::set(auth()->user()->establishment_id);
        $worker = WorkerInput::set($inputs->worker_id);

        $values = [
            'consecutive' => $this->getConsecutive(9, true, $inputs->prefix),
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => auth()->user()->establishment_id,
            'establishment' => $establishment,
            'date_of_issue' => date('Y-m-d'),
            'time_of_issue' => date('H:i:s'),
            'worker' => $worker,
            'state_document_id' => self::REGISTERED, //estado inicial
        ];
 
        return $inputs->merge($values)->all();
    }

        
    /**
     * Enviar nomina a la api
     *
     * @param  DocumentPayroll $document
     * @param  mixed $inputs
     */
    public function sendToApi(DocumentPayroll $document, $inputs)
    {

        $params = $this->getParamsForApi($document, $inputs);
        // dd($params);
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $send_request_to_api = $connection_api->sendRequestToApi("ubl2.1/payroll/{$this->company->test_set_id_payroll}", $params, 'POST');
        $number_full = "{$params['prefix']}-{$params['consecutive']}";

        //error validacion form request api
        if(isset($send_request_to_api['errors']))
        {
            $message = $connection_api->parseErrorsToString($send_request_to_api['errors']);
            $this->throwException($message);
        }

        // validacion respuesta api entorno Pruebas/Produccion
        $this->validateResponseApi($send_request_to_api, $number_full, $connection_api, $document);

        return $send_request_to_api;

    }
    

    /**
     * Validar respuesta al enviar nomina, entorno prueba/produccion
     *
     * @param $send_request_to_api
     * @param $number_full
     * @param HttpConnectionApi $connection_api
     * @param DocumentPayroll $document
     * @return void
     */
    private function validateResponseApi($send_request_to_api, $number_full, HttpConnectionApi $connection_api, DocumentPayroll $document)
    {

        //entorno pruebas/habilitacion
        if($this->company->type_environment_id == 2){

            $zip_key = null;
            // dd($send_request_to_api);
    
            if(array_key_exists('urlpayrollpdf', $send_request_to_api) && array_key_exists('urlpayrollxml', $send_request_to_api))
            {
                
                $send_test_set_async_result = $send_request_to_api['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult'];
                $zip_key = $send_test_set_async_result['ZipKey'];
    
                if(!is_string($zip_key))
                {
                    if(is_string($send_test_set_async_result['ErrorMessageList']['XmlParamsResponseTrackId']['Success']))
                    {
                        if($send_test_set_async_result['ErrorMessageList']['XmlParamsResponseTrackId']['Success'] == 'false')
                        {
                            $this->throwException($send_test_set_async_result['ErrorMessageList']['XmlParamsResponseTrackId']['ProcessedMessage']);
                        }
                    }
                }
            }
            
            // $this->validateZipKey($zip_key, $number_full, $connection_api);

        }
        //entorno produccion
        else{

            //TODO parsear respuesta y verificar
            $send_bill_sync_result = $send_request_to_api['ResponseDian']['Envelope']['Body']['SendBillSyncResponse']['SendBillSyncResult'];

            if($send_bill_sync_result['IsValid'] == "true")
            {
                //estado aceptado en produccion, deberia actualizar un campo en bd para mostrar el mensaje directo de la dian
                $this->updateStateDocument(self::ACCEPTED, $document);
            }
            else
            {
                // estado rechazado
                $extract_error_response = $send_bill_sync_result['ErrorMessage']['string'] ?? $send_bill_sync_result['StatusDescription'];
                $error_message_response = is_array($extract_error_response) ?  implode(",", $extract_error_response) : $extract_error_response;
                $this->throwException("Error al Validar Nómina Nro: {$number_full} Errores: {$error_message_response}");

            }

        }

    }
    
    /**
     * 
     * Actualizar estado de la nómina dependiendo de la validación al enviar a la dian
     *
     * @param  int $state_document_id
     * @param  DocumentPayroll $document
     * @return void
     */
    public function updateStateDocument($state_document_id, DocumentPayroll $document)
    {
        $document->update([
            'state_document_id' => $state_document_id
        ]);
    }

    
    /**
     * 
     * Realiza peticion a ubl2.1/status/zip/{$zip_key} para validar el estado de la nomina (entorno pruebas/habilitacion)
     *
     * @param  string $zip_key
     * @param $number_full
     * @param HttpConnectionApi $connection_api
     * @param DocumentPayroll $document
     * @return void
     */
    public function validateZipKey($zip_key, $number_full, DocumentPayroll $document, HttpConnectionApi $connection_api = null)
    {

        if(!$connection_api){
            $connection_api = new HttpConnectionApi($this->company->api_token);
        }
        
        if($zip_key)
        {
            //esperar 3 segundos para ejecutar servicio status
            // sleep(3);

            $params_zip_key = [
                'is_payroll' => true
            ];

            $query_zip_key = $connection_api->sendRequestToApi("ubl2.1/status/zip/{$zip_key}", $params_zip_key, 'POST');
            // dd($query_zip_key);

            if(isset($query_zip_key['ResponseDian'])){

                $dian_response = $query_zip_key['ResponseDian']['Envelope']['Body']['GetStatusZipResponse']['GetStatusZipResult']['DianResponse'];

                if($dian_response['IsValid'] == "true")
                {
                    //estado aceptado en habilitacion, deberia actualizar un campo en bd para mostrar el mensaje directo de la dian
                    $this->updateStateDocument(self::ACCEPTED, $document);
                }
                else
                {
                    // estado rechazado
                    $extract_error_zip_key = $dian_response['ErrorMessage']['string'] ?? $dian_response['StatusDescription'];
                    $error_message_zip_key = is_array($extract_error_zip_key) ?  implode(",", $extract_error_zip_key) : $extract_error_zip_key;

                    $this->throwException("Error al Validar Nómina Nro: {$number_full} Errores: {$error_message_zip_key}");
                }
            }
            else{
                $this->throwException("Error al Validar Nómina Nro: {$number_full} Errores: {$query_zip_key['message']}");
            }

        }
        else
        {
            $this->throwException('Error de ZipKey.');
        }

    }
    
    
    /**
     * Obtener array para enviar a la api
     *
     * @param  mixed $document
     * @param  mixed $inputs
     * @return array
     */
    public function getParamsForApi($document, $inputs)
    {

        $establishment = $document->establishment;
        $worker = $document->worker;
        $accrued = $document->accrued;
        $deduction = $document->deduction;
        
        // dd($document->period, $inputs);

        return [
            'type_document_id' => 9, //id tipo documento nomina
            'establishment_name' => $establishment->description,
            'establishment_address' => $establishment->address,
            'establishment_phone' => $establishment->telephone,
            'establishment_municipality' => null,
            'establishment_email' => $establishment->email,
            'head_note' => $document->head_note,
            'foot_note' => $document->foot_note,
            'novelty' => $document->novelty ?? [
                'novelty' => false,
                'uuidnov' => ''
            ],
            'period' => $document->period,
            'worker_code' => $worker->code,
            'prefix' => $document->prefix,
            'consecutive' => $document->consecutive,
            'payroll_period_id' => $document->payroll_period_id,
            'notes' => $document->notes,
            'worker' => [
                'type_worker_id' => $worker->type_worker_id,
                'sub_type_worker_id' => $worker->sub_type_worker_id,
                'payroll_type_document_identification_id' => $worker->payroll_type_document_identification_id,
                'municipality_id' => $worker->municipality_id,
                'type_contract_id' => $worker->type_contract_id,
                'high_risk_pension' => $worker->high_risk_pension,
                'identification_number' => $worker->identification_number,
                'surname' => $worker->surname,
                'second_surname' => $worker->second_surname,
                'first_name' => $worker->first_name,
                'address' => $worker->address,
                'integral_salarary' => $worker->integral_salarary,
                'salary' => $worker->salary,
            ],
            'payment' => $document->payment,
            'payment_dates' => $document->payment_dates, 
            'accrued' => [
                'worked_days' => $accrued->worked_days,
                'salary' => $accrued->salary,
                'accrued_total' => $accrued->accrued_total,
                'transportation_allowance' => $accrued->transportation_allowance,
                'telecommuting' => $accrued->telecommuting,
                'work_disabilities' => $accrued->work_disabilities,
            ],
            'deductions' => [
                'eps_type_law_deductions_id' => $deduction->eps_type_law_deductions_id,
                'eps_deduction' => $deduction->eps_deduction,
                'pension_type_law_deductions_id' => $deduction->pension_type_law_deductions_id,
                'pension_deduction' => $deduction->pension_deduction,
                'deductions_total' => $deduction->deductions_total,

                'afc' => $deduction->afc,
                'refund' => $deduction->refund,
                'debt' => $deduction->debt,
                'labor_union' => $deduction->labor_union,
                'education' => $deduction->education,
                'sanctions' => $deduction->sanctions,
            ]
        ];

    }

    
    /**
     * Obtener correlativo desde el api
     *
     * @param  mixed $type_service
     * @param  mixed $ignore_state_document_id
     * @param  mixed $prefix
     */
    public function getConsecutive($type_service, $ignore_state_document_id = false, $prefix = null)
    {
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $url = ($prefix) ? "ubl2.1/payroll/current_number/{$type_service}/{$ignore_state_document_id}/{$prefix}" : "ubl2.1/payroll/current_number/{$type_service}/{$ignore_state_document_id}";
        $send_request_to_api = $connection_api->get($url);

        if(isset($send_request_to_api['success']))
        {
            return $send_request_to_api['number'];
        }

        return null;
    }

        
    /**
     * Envio de correo
     *
     * @param  mixed $request
     * @return array
     */
    public function sendEmail($request)
    {
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $send_request_to_api = $connection_api->sendRequestToApi("ubl2.1/send-email-document-payroll", $request->all(), 'POST');

        return $this->getGeneralResponseFromApi($send_request_to_api, $connection_api);
    }
    
    /**
     * Parsea respuesta api, y retorna arreglo con datos para la vista
     *
     * @param  $response
     * @param  HttpConnectionApi $connection_api
     * @return array
     */
    public function getGeneralResponseFromApi($response, HttpConnectionApi $connection_api)
    {
        
        //error validacion form request api
        if(isset($response['errors']))
        {
            return [
                'success' => false,
                'message' => $connection_api->parseErrorsToString($response['errors'])
            ];
        }

        //si la api retorna true/false, retorna arreglo con estado
        return $response;

    }


    public function throwException($message)
    {
        throw new Exception($message);
    }


}
