<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'beginning_balance' => [
                'required',
                'numeric',
                'min:0',
            ],
            'resolution_id' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'resolution_id.required' => 'El campo Resolución es obligatorio.',
        ];
    }
}
