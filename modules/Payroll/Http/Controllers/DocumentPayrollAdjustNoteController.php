<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Payroll\Models\{
    DocumentPayroll,
    Worker
};
use Modules\Payroll\Http\Resources\{
    DocumentPayrollCollection,
    DocumentPayrollResource
};
use Modules\Payroll\Http\Requests\DocumentPayrollAdjustNoteRequest;
use Modules\Factcolombia1\Models\TenantService\{
    PayrollPeriod,
    TypeLawDeductions,
    TypeDisability,
    AdvancedConfiguration,
    TypeOvertimeSurcharge,
};
use Modules\Factcolombia1\Models\Tenant\{
    PaymentMethod,
    TypeDocument,
};
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Payroll\Helpers\DocumentPayrollHelper;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController;
use Modules\Payroll\Traits\UtilityTrait; 


class DocumentPayrollAdjustNoteController extends Controller
{
    
    use UtilityTrait;

 
    public function tables($type_payroll_adjust_note_id)
    {

        $resolutions = TypeDocument::select('id', 'prefix', 'resolution_number')->where('code', DocumentPayroll::ADJUST_NOTE_TYPE_DOCUMENT_ID)->get();

        // nomina eliminacion
        if($type_payroll_adjust_note_id == 2)
        {
            return [
                'resolutions' => $resolutions
            ];
        }

        // return [
        //     'workers' => $this->table('workers'),
        //     'payroll_periods' => PayrollPeriod::get(),
        //     'type_disabilities' => TypeDisability::get(),
        //     'payment_methods' => PaymentMethod::get(),
        //     'type_law_deductions' => TypeLawDeductions::whereTypeLawDeductionsWorker()->get(),
        //     'advanced_configuration' => AdvancedConfiguration::first(),
        //     'resolutions' => $resolutions
        // ];

    }

     
    /**
     * 
     * Registar nómina de eliminación/reemplazo
     *
     * @param  DocumentPayrollAdjustNoteRequest $request
     * @return array
     */
    public function store(DocumentPayrollAdjustNoteRequest $request)
    {

        try {

            $data = DB::connection('tenant')->transaction(function () use($request) {
    
                // inputs
                $helper = new DocumentPayrollHelper();
                $inputs = $helper->getInputsAdjustNote($request);

                // dd($inputs);
                // registrar nomina en bd
                $document = DocumentPayroll::create($inputs);
                $document->adjust_note()->create($inputs['adjust_note']);
    
                // enviar nomina ajuste a la api
                $send_to_api = $helper->sendToApi($document, $inputs);
    
                $document->update([
                    'response_api' => $send_to_api
                ]);
    
                return $document;
            });

            $message = $data->adjust_note->is_adjust_note_elimination ? "Nómina de eliminación {$data->number_full} registrada con éxito" : "Nómina de reemplazo {$data->number_full} registrada con éxito";
    
            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $data->id
                ]
            ];

        } catch (Exception $e)
        {
            return $this->getErrorFromException($e->getMessage(), $e);
        }

    }
 
        
}
