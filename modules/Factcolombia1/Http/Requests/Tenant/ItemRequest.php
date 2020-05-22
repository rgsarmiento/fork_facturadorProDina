<?php

namespace Modules\Factcolombia1\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\RequestsTrait;

class ItemRequest extends FormRequest
{
    use RequestsTrait;
    
    /**
     * Form
     * @var string
     */
    public $form = 'item';
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|unique:tenant.items,name|max:50',
            'code' => 'required|unique:tenant.items,code|alpha_dash|max:11',
            'type_unit_id' => 'required|exists:tenant.type_units,id',
            'price' => 'required|numeric|between:0.00,9999999999.99',
            'tax_id' => 'nullable|exists:tenant.taxes,id'
        ];
    }
}
