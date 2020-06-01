<?php

namespace Modules\Factcolombia1\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentCollection extends ResourceCollection
{
     

    public function toArray($request) {
        

        return $this->collection->transform(function($row, $key){ 
            return [
                'id' => $row->id, 
                'date_issue' => $row->date_issue, 
                'acknowledgment_received' => ($row->acknowledgment_received != null) ? ($row->acknowledgment_received == 1 ? 'Aceptado' : 'Rechazado'):'', 
                'client_name' => $row->client->name, 
                'client_number' => $row->client->identification_number, 
                'client_type_identity_document' => $row->client->type_identity_document->name, 
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
