<?php

namespace Modules\Payroll\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentPayrollRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');

        return [ 
            'type_document_id' => [
                'required',
            ],
            'prefix' => [
                'required',
            ],
            'payroll_period_id' => [
                'required',
            ],
            'worker_id' => [
                'required',
            ],

            // Period
            'period' => 'required|array',
            'period.admision_date' => 'required|date_format:Y-m-d',
            'period.settlement_start_date' => 'required|date_format:Y-m-d',
            'period.settlement_end_date' => 'required|date_format:Y-m-d',
            'period.worked_time' => 'required|numeric',
            'period.issue_date' => 'required|date_format:Y-m-d',

            // Payment
            'payment' => 'required|array',
            'payment.payment_method_id' => 'required',
            'payment.bank_name' => 'nullable|required_if:payment.payment_method_id,2,3,4,5,6,7,21,22,30,31,42,45,46,47,|string',
            'payment.account_type' => 'nullable|required_if:payment.payment_method_id,2,3,4,5,6,7,21,22,30,31,42,45,46,47,|string',
            'payment.account_number' => 'nullable|required_if:payment.payment_method_id,2,3,4,5,6,7,21,22,30,31,42,45,46,47,|string',


            // Payment Dates
            'payment_dates' => 'required|array',
            'payment_dates.*.payment_date' => 'required|date_format:Y-m-d',

 
            // Accrued
            'accrued' => 'required|array',
            'accrued.worked_days' => 'required|numeric|digits_between:1,2',
            'accrued.salary' => 'required|numeric',
            'accrued.accrued_total' => 'required|numeric',
            
            // heds
            'accrued.heds' => 'nullable|array',
            'accrued.heds.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.heds.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.heds.*.quantity' => 'required|numeric',
            'accrued.heds.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.heds.*.payment' => 'required|numeric',

            // hens
            'accrued.hens' => 'nullable|array',
            'accrued.hens.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hens.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hens.*.quantity' => 'required|numeric',
            'accrued.hens.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.hens.*.payment' => 'required|numeric',
            
            // hrns
            'accrued.hrns' => 'nullable|array',
            'accrued.hrns.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrns.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrns.*.quantity' => 'required|numeric',
            'accrued.hrns.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.hrns.*.payment' => 'required|numeric',
            
            // heddfs
            'accrued.heddfs' => 'nullable|array',
            'accrued.heddfs.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.heddfs.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.heddfs.*.quantity' => 'required|numeric',
            'accrued.heddfs.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.heddfs.*.payment' => 'required|numeric',
            
            // hrddfs
            'accrued.hrddfs' => 'nullable|array',
            'accrued.hrddfs.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrddfs.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrddfs.*.quantity' => 'required|numeric',
            'accrued.hrddfs.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.hrddfs.*.payment' => 'required|numeric',

            // hendfs
            'accrued.hendfs' => 'nullable|array',
            'accrued.hendfs.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hendfs.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hendfs.*.quantity' => 'required|numeric',
            'accrued.hendfs.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.hendfs.*.payment' => 'required|numeric',

            // hrndfs
            'accrued.hrndfs' => 'nullable|array',
            'accrued.hrndfs.*.start_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrndfs.*.end_time' => 'required|date_format:Y-m-d\TH:i:s',
            'accrued.hrndfs.*.quantity' => 'required|numeric',
            'accrued.hrndfs.*.percentage' => 'required|exists:tenant.co_type_overtime_surcharges,id',
            'accrued.hrndfs.*.payment' => 'required|numeric',

            // Deductions
            'deduction' => 'required|array',
            'deduction.eps_type_law_deductions_id' => 'required',
            'deduction.eps_deduction' => 'required|numeric',
            'deduction.pension_type_law_deductions_id' => 'required',
            'deduction.pension_deduction' => 'required|numeric',
            'deduction.deductions_total' => 'required|numeric',
        ];
    }

}