<?php

namespace App\Http\Controllers\Tenant;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Exports\Tenant\TaxesExport;
use App\Http\Requests\Tenant\{
    TaxUpdateRequest,
    TaxRequest
};
use Illuminate\Http\Request;
use App\Models\Tenant\{
    Company,
    Tax
};
use App\Models\TenantService\{
    Tax as TypeTaxes
};

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('tax.tenant.index');
    }
    
    /**
     * All
     * @return \Illuminate\Http\Response
     */
    public function all() {
        return [
            'company' => Company::query()
                ->with('currency')
                ->firstOrFail(),
            'taxes' => Tax::query()
                ->with('tax')
                ->get(),
            'type_taxes' => TypeTaxes::all()
        ];
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\TaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxRequest $request) {
        $tax = Tax::create([
            'name' => strtoupper($request->name),
            'code' => $request->code,
            'rate' => $request->rate ?? 0,
            'conversion' => $request->conversion,
            'is_percentage' => $request->is_percentage,
            'is_fixed_value' => $request->is_fixed_value,
            'is_retention' => $request->is_retention,
            'in_base' => $request->in_base,
            'in_tax' => $request->in_tax,
            'type_tax_id' => $request->type_tax_id
        ]);
        
        return [
            'success' => true,
            'message' => "Se registro con éxito el impuesto {$tax->name}."
        ];
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Requests\Tenant\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update(TaxUpdateRequest $request, Tax $tax) {
        $tax->name = strtoupper($request->name);
        $tax->code = $request->code;
        $tax->rate = $request->rate ?? 0;
        $tax->conversion = $request->conversion;
        $tax->is_percentage = $request->is_percentage;
        $tax->is_fixed_value = $request->is_fixed_value;
        $tax->is_retention = $request->is_retention;
        $tax->in_base = $request->in_base;
        $tax->in_tax = $request->in_tax;
        $tax->type_tax_id = $request->type_tax_id;
        
        $tax->save();
        
        return [
            'success' => true,
            'message' => "Se actualizo con éxito el impuesto {$tax->name}."
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\Tenant\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tax $tax) {
        $tax->forceDelete();
        
        return [
            'success' => true,
            'message' => "Se elimino con éxito el impuesto {$tax->name}."
        ];
    }
    
    /**
     * Export
     * @return \Illuminate\Http\Response
     */
    public function export() {
        return Excel::download(new TaxesExport, 'impuestos.xlsx');
    }
}
