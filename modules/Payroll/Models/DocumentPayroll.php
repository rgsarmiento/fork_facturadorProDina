<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant;
use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
};
use Modules\Factcolombia1\Models\TenantService\{
    PayrollPeriod
};
use App\Models\Tenant\{
    Establishment
};


class DocumentPayroll extends ModelTenant
{

    protected $table = 'co_document_payrolls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 

        'external_id',
        'date_of_issue',
        'time_of_issue',

        'type_document_id',
        'establishment_id',
        'establishment',

        'head_note',
        'foot_note',

        'novelty',

        'period',
        
        'prefix',
        'consecutive',

        'payroll_period_id',
        'notes',
        
        'worker_id',
        'worker',
        
        'payment',
        'payment_dates',

    ];
        
    protected $casts = [
        'date_of_issue' => 'date',
    ];


    public function getEstablishmentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    }

    public function getNoveltyAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setNoveltyAttribute($value)
    {
        $this->attributes['novelty'] = (is_null($value))?null:json_encode($value);
    }

    public function getPeriodAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPeriodAttribute($value)
    {
        $this->attributes['period'] = (is_null($value))?null:json_encode($value);
    }

    public function getWorkerAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setWorkerAttribute($value)
    {
        $this->attributes['Worker'] = (is_null($value))?null:json_encode($value);
    }

    public function getPaymentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPaymentAttribute($value)
    {
        $this->attributes['Payment'] = (is_null($value))?null:json_encode($value);
    }

    public function getPaymentDatesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPaymentDatesAttribute($value)
    {
        $this->attributes['payment_dates'] = (is_null($value))?null:json_encode($value);
    }

    public function type_document() 
    {
        return $this->belongsTo(TypeDocument::class);
    }

    public function establishment() 
    {
        return $this->belongsTo(Establishment::class);
    }

    public function payroll_period() 
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function worker() 
    {
        return $this->belongsTo(Worker::class);
    }

    public function accrued() 
    {
        return $this->hasOne(DocumentPayrollAccrued::class);
    }

    public function deduction() 
    {
        return $this->hasOne(DocumentPayrollDeduction::class);
    }

    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $this->time_of_issue,
            'type_document_id' => $this->type_document_id,
            'establishment_id' => $this->establishment_id,
            'establishment' => $this->establishment,
            'head_note' => $this->head_note,
            'foot_note' => $this->foot_note,
            'novelty' => $this->novelty,
            'period' => $this->period,
            'prefix' => $this->prefix,
            'consecutive' => $this->consecutive,
            'payroll_period_id' => $this->payroll_period_id,
            'notes' => $this->notes,
            'worker_id' => $this->worker_id,
            'worker' => $this->worker,
            'payment' => $this->payment,
            'payment_dates' => $this->payment_dates,
        ];

    }

}
