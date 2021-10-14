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
            'type_documents' => TypeDocument::get(),
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
    
    public function records(Request $request)
    {
        $records = DocumentPayroll::where($request->column, 'like', "%{$request->value}%")->latest();

        return new DocumentPayrollCollection($records->paginate(config('tenant.items_per_page')));
    }
 

    public function store(DocumentPayrollRequest $request)
    {

        dd($request->all());
        $id = $request->input('id');
        $record = DocumentPayroll::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Empleado editado con éxito':'Empleado registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $record = DocumentPayroll::findOrFail($id);
        $record->delete(); 

        return [
            'success' => true,
            'message' => 'Empleado eliminado con éxito'
        ];
    }

}
