<?php

namespace Modules\Payroll\Helpers;

use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use Illuminate\Support\Str;
use Modules\Factcolombia1\Helpers\HttpConnectionApi;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceCompany
};
use Exception;

class DocumentPayrollHelper
{

    private $company;

    public function __construct()
    {
        $this->company = ServiceCompany::select('test_set_id_payroll', 'api_token')->firstOrFail();
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
            'consecutive' => $this->getConsecutive(9, $inputs->prefix),
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => auth()->user()->establishment_id,
            'establishment' => $establishment,
            'date_of_issue' => date('Y-m-d'),
            'time_of_issue' => date('H:i:s'),
            'worker' => $worker,

        ];
 
        return $inputs->merge($values)->all();
    }

        
    /**
     * Enviar nomina a la api
     *
     * @param  mixed $document
     * @param  mixed $inputs
     */
    public function sendToApi($document, $inputs)
    {

        $params = $this->getParamsForApi($document, $inputs);
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $send_request_to_api = $connection_api->sendRequestToApi("ubl2.1/payroll/{$this->company->test_set_id_payroll}", $params, 'POST');

        //error validacion form request api
        if(isset($send_request_to_api['errors']))
        {
            $message = $connection_api->parseErrorsToString($send_request_to_api['errors']);
            throw new Exception($message);
        }


        //TODO no se esta validando tipo de entorno, pruebas o produccion
        $zip_key = null;
        // dd($send_request_to_api);

        if(array_key_exists('urlpayrollpdf', $send_request_to_api) && array_key_exists('urlpayrollxml', $send_request_to_api))
        {
            $zip_key = $send_request_to_api['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ZipKey'];

            if(!is_string( $zip_key))
            {
                if(is_string($send_request_to_api['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ErrorMessageList']['XmlParamsResponseTrackId']['Success']))
                {
                    if($send_request_to_api['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ErrorMessageList']['XmlParamsResponseTrackId']['Success'] == 'false')
                    {
                        throw new Exception($send_request_to_api['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ErrorMessageList']['XmlParamsResponseTrackId']['ProcessedMessage']);
                    }
                }
            }
        }

        if($zip_key)
        {
            //se deberia ejecutar servicio de status document 
        }
        else
        {
            throw new Exception('Error de ZipKey.');
        }

        return $send_request_to_api;

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
            ],
            'deductions' => [
                'eps_type_law_deductions_id' => $deduction->eps_type_law_deductions_id,
                'eps_deduction' => $deduction->eps_deduction,
                'pension_type_law_deductions_id' => $deduction->pension_type_law_deductions_id,
                'pension_deduction' => $deduction->pension_deduction,
                'deductions_total' => $deduction->deductions_total,
            ]
        ];

    }

    
    /**
     * Obtener correlativo desde el api
     *
     * @param  mixed $type_service
     * @param  mixed $prefix
     */
    public function getConsecutive($type_service, $prefix = null)
    {
        $connection_api = new HttpConnectionApi($this->company->api_token);
        $url = ($prefix) ? "ubl2.1/payroll/current_number/{$type_service}/{$prefix}" : "ubl2.1/payroll/current_number/{$type_service}";
        $send_request_to_api = $connection_api->get($url);

        if(isset($send_request_to_api['success']))
        {
            return $send_request_to_api['number'];
        }

        return null;
    }

}
