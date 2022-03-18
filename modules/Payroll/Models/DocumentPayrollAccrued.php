<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant;
use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
};


class DocumentPayrollAccrued extends ModelTenant
{

    protected $table = 'co_document_payroll_accrued';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 

        'co_document_payroll_id',
        'worked_days',
        'salary',
        'transportation_allowance',

        'heds',
        'hens',
        'hrns',
        'heddfs',
        'hrddfs',
        'hendfs',
        'hrndfs',

        'common_vacation',
        'paid_vacation',
        'service_bonus',
        'severance',

        'work_disabilities',
        'maternity_leave',
        'paid_leave',
        'non_paid_leave',

        'bonuses',
        'aid',
        'legal_strike',
        'other_concepts',

        'compensations',
        'epctv_bonuses',
        'commissions',
        'third_party_payments',
        'advances',
        
        'endowment',
        'sustenance_support',
        'telecommuting',
        'withdrawal_bonus',
        'compensation',
        'accrued_total',

    ];


    public function getHedsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHedsAttribute($value)
    {
        $this->attributes['heds'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHensAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHensAttribute($value)
    {
        $this->attributes['hens'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHrnsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHrnsAttribute($value)
    {
        $this->attributes['hrns'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHeddfsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHeddfsAttribute($value)
    {
        $this->attributes['heddfs'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHrddfsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHrddfsAttribute($value)
    {
        $this->attributes['hrddfs'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHendfsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHendfsAttribute($value)
    {
        $this->attributes['hendfs'] = (is_null($value) || empty($value))?null:json_encode($value);
    }

    public function getHrndfsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setHrndfsAttribute($value)
    {
        $this->attributes['hrndfs'] = (is_null($value) || empty($value))?null:json_encode($value);
    }
    
    public function getCommonVacationAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCommonVacationAttribute($value)
    {
        $this->attributes['common_vacation'] = $this->getArrayValueAndValidate($value);
    }

    public function getPaidVacationAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPaidVacationAttribute($value)
    {
        $this->attributes['paid_vacation'] = $this->getArrayValueAndValidate($value);
    }

    public function getServiceBonusAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setServiceBonusAttribute($value)
    {
        $this->attributes['service_bonus'] = $this->getArrayValueAndValidate($value);
    }

    public function getSeveranceAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setSeveranceAttribute($value)
    {
        $this->attributes['severance'] = $this->getArrayValueAndValidate($value);
    }

    public function getWorkDisabilitiesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setWorkDisabilitiesAttribute($value)
    {
        $this->attributes['work_disabilities'] = (is_null($value) || empty($value)) ? null : json_encode($value);
    }
 
    public function getMaternityLeaveAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setMaternityLeaveAttribute($value)
    {
        $this->attributes['maternity_leave'] = (is_null($value))?null:json_encode($value);
    }

    public function getPaidLeaveAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPaidLeaveAttribute($value)
    {
        $this->attributes['paid_leave'] = (is_null($value))?null:json_encode($value);
    }
 
    public function getNonPaidLeaveAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setNonPaidLeaveAttribute($value)
    {
        $this->attributes['non_paid_leave'] = (is_null($value))?null:json_encode($value);
    }

    public function getBonusesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setBonusesAttribute($value)
    {
        $this->attributes['bonuses'] = $this->getArrayValueAndValidate($value);
    }

    public function getAidAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setAidAttribute($value)
    {
        $this->attributes['aid'] = $this->getArrayValueAndValidate($value);
    }

    public function getLegalStrikeAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setLegalStrikeAttribute($value)
    {
        $this->attributes['legal_strike'] = (is_null($value))?null:json_encode($value);
    }
    
    public function getOtherConceptsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setOtherConceptsAttribute($value)
    {
        $this->attributes['other_concepts'] = $this->getArrayValueAndValidate($value);
    }

    public function getCompensationsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCompensationsAttribute($value)
    {
        $this->attributes['compensations'] = (is_null($value))?null:json_encode($value);
    }

    public function getEpctvBonusesAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEpctvBonusesAttribute($value)
    {
        $this->attributes['epctv_bonuses'] = (is_null($value))?null:json_encode($value);
    }

    public function getCommissionsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCommissionsAttribute($value)
    {
        $this->attributes['commissions'] = (is_null($value))?null:json_encode($value);
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
        $this->attributes['Advances'] = (is_null($value))?null:json_encode($value);
    }

    /**
     * 
     * Validar dato y retornar valor correspondiente para campos tipo json
     *
     * @param $value
     */
    public function getArrayValueAndValidate($value)
    {
        return (is_null($value) || empty($value)) ? null : json_encode($value);
    }

    public function payroll() 
    {
        return $this->belongsTo(DocumentPayroll::class, 'co_document_payroll_id');
    }
    
    /**
     * Retorna data de las horas extras con los campos necesarios para enviar a la api
     *
     * @param  array $records
     * @return array
     */
    public function parseExtraHoursToFormatApi($records)
    {
        if(empty($records)) return null;

        return collect($records)
                ->map(function($row, $key){
                    return [
                        'start_time' => $row->start_time,
                        'end_time' => $row->end_time,
                        'quantity' => $row->quantity,
                        'percentage' => $row->percentage,
                        'payment' => $row->payment
                    ];
                })
                ->toArray();
    }

    
    /**
     * Retorna data de las incapacidades con los campos necesarios para enviar a la api
     *
     * @param  array $records
     * @return array
     */
    public function parseWorkDisabilitiesToFormatApi($records)
    {
        if(empty($records)) return null;

        return collect($records)
                ->map(function($row, $key){
                    return [
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'type' => $row->type,
                        'quantity' => $row->quantity,
                        'payment' => $row->payment
                    ];
                })
                ->toArray();
    }

    /**
     * Retorna data de las vaciones disfrutadas con los campos necesarios para enviar a la api
     *
     * Usado para vacaciones compensadas y disfrutadas
     * 
     * @param  array $records
     * @return array
     */
    public function parseCommonVacationToFormatApi($records)
    {
        if(empty($records)) return null;

        return collect($records)
                ->map(function($row, $key){
                    return [
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'quantity' => $row->quantity,
                        'payment' => $row->payment
                    ];
                })
                ->toArray();
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
            'worked_days' => $this->worked_days,
            'salary' => $this->salary,
            'transportation_allowance' => $this->transportation_allowance,

            'heds' => $this->heds,
            'hens' => $this->hens,
            'hrns' => $this->hrns,
            'heddfs' => $this->heddfs,
            'hrddfs' => $this->hrddfs,
            'hendfs' => $this->hendfs,
            'hrndfs' => $this->hrndfs,

            'common_vacation' => $this->common_vacation,
            'paid_vacation' => $this->paid_vacation,
            'service_bonus' => $this->service_bonus,
            'severance' => $this->severance,

            'work_disabilities' => $this->work_disabilities,
            'maternity_leave' => $this->maternity_leave,
            'paid_leave' => $this->paid_leave,
            'non_paid_leave' => $this->non_paid_leave,

            'bonuses' => $this->bonuses,
            'aid' => $this->aid,
            'legal_strike' => $this->legal_strike,
            'other_concepts' => $this->other_concepts,

            'compensations' => $this->compensations,
            'epctv_bonuses' => $this->epctv_bonuses,
            'commissions' => $this->commissions,
            'third_party_payments' => $this->third_party_payments,
            'advances' => $this->advances,
            
            'endowment' => $this->endowment,
            'sustenance_support' => $this->sustenance_support,
            'telecommuting' => $this->telecommuting,
            'withdrawal_bonus' => $this->withdrawal_bonus,
            'compensation' => $this->compensation,
            'accrued_total' => $this->accrued_total,

        ];

    }

}
