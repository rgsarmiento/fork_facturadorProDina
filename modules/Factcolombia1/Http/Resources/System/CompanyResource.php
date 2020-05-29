<?php

namespace Modules\Factcolombia1\Http\Resources\System;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\System\Module;
use Modules\Factcolombia1\Models\SystemService\{
    Company as ServiceCompany
};

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    { 

        $service_company = ServiceCompany::where('identification_number', $this->identification_number)->first();

        return [
            'id' => $this->id,
            'identification_number' => $this->identification_number,
            'name' => $this->name,
            'email' => $this->email,
            'subdomain' => $this->subdomain, 
            'limit_documents' => $this->limit_documents, 
            'hostname_id' => $this->hostname_id, 
            'ica_rate' => $this->ica_rate, 
            'economic_activity_code' => $this->economic_activity_code, 

            'user_id' => $service_company->user_id, 
            'dv' => $service_company->dv, 
            'language_id' => $service_company->language_id, 
            'tax_id' => $service_company->tax_id, 
            'type_environment_id' => $service_company->type_environment_id, 
            'type_operation_id' => $service_company->type_operation_id, 
            'type_document_identification_id' => $service_company->type_document_identification_id, 
            'country_id' => $service_company->country_id, 
            'department_id' => $service_company->department_id, 
            'type_currency_id' => $service_company->type_currency_id, 
            'type_organization_id' => $service_company->type_organization_id, 
            'type_regime_id' => $service_company->type_regime_id, 
            'type_liability_id' => $service_company->type_liability_id, 
            'municipality_id' => $service_company->municipality_id, 
            'merchant_registration' => $service_company->merchant_registration, 
            'address' => $service_company->address, 
            'phone' => $service_company->phone, 


        ];

    }
}
