<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant;
use Modules\Factcolombia1\Models\TenantService\{
    TypeWorker,
    SubTypeWorker,
    PayrollTypeDocumentIdentification,
    TypeContract,
    Municipality
};


class Worker extends ModelTenant
{

    protected $table = 'co_workers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'type_worker_id',
        'sub_type_worker_id',
        'payroll_type_document_identification_id',
        'municipality_id',
        'type_contract_id',
        'identification_number',
        'surname',
        'second_surname',
        'first_name',
        'address',
        'high_risk_pension',
        'integral_salarary',
        'salary'
    ];

        
    protected $casts = [
        'high_risk_pension' => 'boolean',
        'integral_salarary' => 'boolean', 
    ];


    public function type_worker()
    {
        return $this->belongsTo(TypeWorker::class);
    }
    
    public function sub_type_worker()
    {
        return $this->belongsTo(SubTypeWorker::class);
    }
    
    public function payroll_type_document_identification()
    {
        return $this->belongsTo(PayrollTypeDocumentIdentification::class);
    }
    
    public function type_contract()
    {
        return $this->belongsTo(TypeContract::class);
    }
    
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function getSearchFullNameAttribute()
    { 
        return "{$this->identification_number} - {$this->second_surname} {$this->surname} {$this->first_name}";
    }
 
    public function getSearchRowResource()
    { 
        return [
            'id' => $this->id,
            'search_fullname' => $this->search_fullname,
        ];
    }
 

    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        return [
            'id' => $this->id,
            'code' => $this->code,
            'type_worker_id' => $this->type_worker_id,
            'sub_type_worker_id' => $this->sub_type_worker_id,
            'payroll_type_document_identification_id' => $this->payroll_type_document_identification_id,
            'municipality_id' => $this->municipality_id,
            'type_contract_id' => $this->type_contract_id,
            'identification_number' => $this->identification_number,
            'surname' => $this->surname,
            'second_surname' => $this->second_surname,
            'first_name' => $this->first_name,
            'address' => $this->address,
            'high_risk_pension' => $this->high_risk_pension,
            'integral_salarary' => $this->integral_salarary,
            'salary' => $this->salary,
        ];

    }

}
