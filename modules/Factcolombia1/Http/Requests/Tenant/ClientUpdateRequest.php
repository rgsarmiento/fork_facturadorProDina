<?php

namespace Modules\Factcolombia1\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Tenant\RequestsTrait;

class ClientUpdateRequest extends FormRequest
{
    use RequestsTrait;
    
    /**
     * Form
     * @var string
     */
    public $form = 'client';
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()     {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'type_person_id' => 'nullable|exists:tenant.type_people,id',
            'type_regime_id' => 'nullable|exists:tenant.type_regimes,id',
            'name' => 'required|max:50',
            'country_id' => 'nullable|exists:tenant.countries,id',
            'department_id' => 'nullable|exists:tenant.departments,id',
            'city_id' => 'nullable|exists:tenant.cities,id',
            'address' => 'nullable|max:50',
            'phone' => 'nullable|integer|digits_between:7,10',
            'email' => 'nullable|email|max:50'
        ];
    }
}
