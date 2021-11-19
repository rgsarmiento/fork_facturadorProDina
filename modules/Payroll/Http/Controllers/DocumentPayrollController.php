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
use Modules\Payroll\Http\Requests\DocumentPayrollRequest;
use Modules\Factcolombia1\Models\TenantService\{
    PayrollPeriod,
    TypeLawDeductions
};
use Modules\Factcolombia1\Models\Tenant\{
    PaymentMethod,
    TypeDocument
};
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Payroll\Helpers\DocumentPayrollHelper;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController;


class DocumentPayrollController extends Controller
{
    
    public function index()
    {
        return view('payroll::document-payrolls.index');
    }

    public function create()
    {
        return view('payroll::document-payrolls.form');
    }

    public function columns()
    {
        return [
            'consecutive' => 'Número',
            'date_of_issue' => 'Fecha de emisión',
        ];
    }
 
    public function tables()
    {
        return [
            'workers' => $this->table('workers'),
            'payroll_periods' => PayrollPeriod::get(),
            'payment_methods' => PaymentMethod::get(),
            'type_law_deductions' => TypeLawDeductions::get(),
            // 'type_documents' => TypeDocument::get(),
            'resolutions' => TypeDocument::select('id','prefix', 'resolution_number')->where('code', 9)->get()
        ];
    }

    public function table($table)
    {

        if($table == 'workers') 
        {
            return Worker::take(20)->get()->transform(function($row){
                return $row->getSearchRowResource();
            });
        }

        return [];
    }
    
    
    public function record($id)
    {
        return new DocumentPayrollResource(DocumentPayroll::findOrFail($id));
    }


    public function records(Request $request)
    {
        $records = DocumentPayroll::where($request->column, 'like', "%{$request->value}%")->latest();

        return new DocumentPayrollCollection($records->paginate(config('tenant.items_per_page')));
    }
 

    public function store(DocumentPayrollRequest $request)
    {

        $data = DB::connection('tenant')->transaction(function () use($request) {

            // inputs
            $helper = new DocumentPayrollHelper();
            $inputs = $helper->getInputs($request);
            
            // registrar nomina en bd
            $document = DocumentPayroll::create($inputs);
            $document->accrued()->create($inputs['accrued']);
            $document->deduction()->create($inputs['deduction']);

            // enviar nomina a la api
            $send_to_api = $helper->sendToApi($document, $inputs);

            $document->update([
                'response_api' => $send_to_api
            ]);

            return $document;
        });

        return [
            'success' => true,
            'message' => 'Nómina registrada con éxito',
            'data' => [
                'id' => $data->id
            ]
        ];
    }
 
    public function downloadFile($filename)
    {
        return app(DocumentController::class)->downloadFile($filename);
    }

}