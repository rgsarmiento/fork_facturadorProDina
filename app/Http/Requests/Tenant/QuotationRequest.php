<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationRequest extends FormRequest
{
     
    public function authorize()
    {
        return true; 
    }
 
    public function rules()
    { 
        
        return [
            'customer_id' => [
                'required',
            ],
            'exchange_rate_sale' => [
                'required',
                'numeric'
            ],
            'currency_id' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ], 
        ];
    }
}
