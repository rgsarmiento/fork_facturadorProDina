<?php

namespace Modules\Payroll\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkerRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');

        return [
            'code' => [
                'required',
                Rule::unique('tenant.co_workers')->ignore($id),
            ], 
            'type_worker_id' => [
                'required',
            ],
            'sub_type_worker_id' => [
                'required',
            ],
            'payroll_type_document_identification_id' => [
                'required',
            ],
            'municipality_id' => [
                'required',
            ],
            'type_contract_id' => [
                'required',
            ],
            'identification_number' => [
                'required',
                'numeric',
                'digits_between:1,15',
            ],

            'surname' => [
                'required',
            ],
            'second_surname' => [
                'required',
            ],
            'first_name' => [
                'required',
            ],
            'address' => [
                'required',
            ],
            'high_risk_pension' => [
                'required',
            ],
            'integral_salarary' => [
                'required',
            ],
            'salary' => [
                'required',
            ], 
            
        ];
    }

}