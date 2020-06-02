<?php

namespace Modules\Factcolombia1\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceTenantCompany
};

class DocumentResource extends JsonResource
{
     

    public function toArray($request) {

        return [
            'id' => $this->id, 
            'correlative_api' => $this->correlative_api, 
            'number_full' => $this->number_full, 
            'client_email' => $this->client->email, 
            'client_phone' => $this->client->phone, 

        ];
            
    }
}
