<?php

namespace Modules\Factcolombia1\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Factcolombia1\Models\TenantService\{
    Company as ServiceTenantCompany
};

class DocumentCollection extends ResourceCollection
{
     

    public function toArray($request) {

        $company = ServiceTenantCompany::firstOrFail();

        return $this->collection->transform(function($row, $key) use ($company){ 
        
            $base_url_api = config('tenant.service_fact');
            $download_xml = "{$base_url_api}download/{$company->identification_number}/{$row->response_api_invoice->urlinvoicexml}";
            $download_pdf = "{$base_url_api}download/{$company->identification_number}/{$row->response_api_invoice->urlinvoicepdf}";


            return [
                'id' => $row->id, 
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'), 
                'download_xml' => $download_xml, 
                'download_pdf' => $download_pdf, 
                'response_api_invoice' => $row->response_api_invoice, 
                'acknowledgment_received' => ($row->acknowledgment_received != null) ? ($row->acknowledgment_received == 1 ? 'Aceptado' : 'Rechazado'):'', 
                'customer_name' => $row->customer->name, 
                'customer_number' => $row->customer->number, 
                'customer_identity_document_type' => $row->customer->identity_document_type->name, 
                'number_full' => "{$row->prefix}-{$row->number}", 
                'type_document_name' => $row->type_document->name, 
                'state_document_name' => $row->state_document->name, 
                'currency_name' => $row->currency->name, 
                'sale' => $row->sale, 
                'total_discount' => $row->total_discount, 
                'total_tax' => $row->total_tax, 
                'subtotal' => $row->subtotal, 
                'total' => $row->total, 

            ];
            
        });
    }
}
