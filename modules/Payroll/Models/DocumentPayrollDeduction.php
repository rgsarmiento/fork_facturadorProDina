<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant; 
use Modules\Factcolombia1\Models\TenantService\{
    TypeLawDeductions
};


class DocumentPayrollDeduction extends ModelTenant
{

    protected $table = 'co_document_payroll_deductions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 

        'co_document_payroll_id',
        'eps_type_law_deductions_id',
        'eps_deduction',
        'pension_type_law_deductions_id',
        'pension_deduction',
        'labor_union',
        'sanctions',
        'orders',
        'third_party_payments',
        'advances',
        'other_deductions',
        'voluntary_pension',
        'withholding_at_source',
        'afc',
        'cooperative',
        'tax_liens',
        'supplementary_plan',
        'education',
        'refund',
        'debt',
        'deductions_total',

    ];
        

    public function getLaborUnionAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLaborUnionAttribute($value)
    {
        $this->attributes['labor_union'] = (is_null($value))?null:json_encode($value);
    }

    public function getSanctionsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSanctionsAttribute($value)
    {
        $this->attributes['sanctions'] = (is_null($value))?null:json_encode($value);
    }

    public function getOrdersAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOrdersAttribute($value)
    {
        $this->attributes['orders'] = (is_null($value))?null:json_encode($value);
    }

    public function getThirdPartyPaymentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setThirdPartyPaymentsAttribute($value)
    {
        $this->attributes['third_party_payments'] = (is_null($value))?null:json_encode($value);
    }

    public function getAdvancesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setAdvancesAttribute($value)
    {
        $this->attributes['advances'] = (is_null($value))?null:json_encode($value);
    }

    public function getOtherDeductionsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOtherDeductionsAttribute($value)
    {
        $this->attributes['other_deductions'] = (is_null($value))?null:json_encode($value);
    }


    public function eps_type_law_deduction() 
    {
        return $this->belongsTo(TypeLawDeductions::class, 'eps_type_law_deductions_id');
    }

    public function pension_type_law_deduction() 
    {
        return $this->belongsTo(TypeLawDeductions::class, 'pension_type_law_deductions_id');
    }

    public function payroll() 
    {
        return $this->belongsTo(DocumentPayroll::class, 'co_document_payroll_id');
    }

    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        return [
            'id' => $this->id, 
            'co_document_payroll_id' => $this->co_document_payroll_id,
            'eps_type_law_deductions_id' => $this->eps_type_law_deductions_id,
            'eps_deduction' => $this->eps_deduction,
            'pension_type_law_deductions_id' => $this->pension_type_law_deductions_id,
            'pension_deduction' => $this->pension_deduction,
            'labor_union' => $this->labor_union,
            'sanctions' => $this->sanctions,
            'orders' => $this->orders,
            'third_party_payments' => $this->third_party_payments,
            'advances' => $this->advances,
            'other_deductions' => $this->other_deductions,
            'voluntary_pension' => $this->voluntary_pension,
            'withholding_at_source' => $this->withholding_at_source,
            'afc' => $this->afc,
            'cooperative' => $this->cooperative,
            'tax_liens' => $this->tax_liens,
            'supplementary_plan' => $this->supplementary_plan,
            'education' => $this->education,
            'refund' => $this->refund,
            'debt' => $this->debt,
            'deductions_total' => $this->deductions_total,
        ];

    }

}
