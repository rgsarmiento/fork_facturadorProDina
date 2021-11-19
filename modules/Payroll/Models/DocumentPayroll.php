<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\{
    ModelTenant,
    User
};
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
        'user_id',
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
        'response_api',

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
        return (is_null($value))?null:json_decode($value, true);
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
        $this->attributes['worker'] = (is_null($value))?null:json_encode($value);
    }

    public function getPaymentAttribute($value)
    {
        return (is_null($value))?null: json_decode($value, true);
    }

    public function setPaymentAttribute($value)
    {
        $this->attributes['payment'] = (is_null($value))?null:json_encode($value);
    }

    public function getPaymentDatesAttribute($value)
    {
        return (is_null($value))?null: json_decode($value, true);
    }

    public function setPaymentDatesAttribute($value)
    {
        $this->attributes['payment_dates'] = (is_null($value))?null:json_encode($value);
    }
    
    public function getResponseApiAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setResponseApiAttribute($value)
    {
        $this->attributes['response_api'] = (is_null($value))?null:json_encode($value);
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

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function model_worker() 
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function accrued() 
    {
        return $this->hasOne(DocumentPayrollAccrued::class, 'co_document_payroll_id');
    }

    public function deduction() 
    {
        return $this->hasOne(DocumentPayrollDeduction::class, 'co_document_payroll_id');
    }

    public function getNumberFullAttribute()
    {
        return $this->prefix.'-'.$this->consecutive;
    }

    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        $filename_xml = null;
        $filename_pdf = null;
        
        if($this->response_api){
            $response = $this->response_api;
            $response_api_message = isset($response->message) ? $response->message:null;

            $filename_xml = $response->urlpayrollxml ?? null;
            $filename_pdf =  $response->urlpayrollpdf ?? null;

        }


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
            'number_full' => $this->number_full,
            'payroll_period_id' => $this->payroll_period_id,
            'notes' => $this->notes,
            'worker_id' => $this->worker_id,
            'worker' => $this->worker,
            'worker_full_name' => $this->model_worker->full_name,
            'payment' => $this->payment,
            'payment_dates' => $this->payment_dates,
            'response_api_message' => $response_api_message,
            // 'response_api' => $this->response_api,

            'salary' => optional($this->accrued)->salary,
            'accrued_total' => optional($this->accrued)->accrued_total,
            'deductions_total' => optional($this->deduction)->deductions_total,

            'filename_xml' => $filename_xml,
            'filename_pdf' => $filename_pdf,

        ];

    }

}