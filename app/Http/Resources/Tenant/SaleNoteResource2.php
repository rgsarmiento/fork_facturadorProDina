<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\SaleNote;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Tenant\Series;


class SaleNoteResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $serie = Series::where('number', $this->series)->first();
        return [
            'number' => $this->number,
            'series_id' => ($serie) ? $serie->id : null,
            'id' => $this->id,
            'prefix' => $this->prefix,
            'establishment_id' => $this->establishment_id,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $this->time_of_issue,
            'customer_id' => $this->customer_id,
            'currency_id' => $this->currency_id,
            'purchase_order' => $this->purchase_order,
            'exchange_rate_sale' => $this->exchange_rate_sale,
            'total' => $this->total,
            'operation_type_id' => $this->operation_type_id,
            'date_of_due' => $this->date_of_due,
            'items' => $this->items,
            'payments' => self::getTransformPayments($this->payments),
            'charges' => $this->charges,
            'discounts' => $this->discounts,
            'attributes' => $this->attributes,
            'guides' => $this->guides,
            'additional_information' => $this->additional_information,
            'quantity_period' => $this->quantity_period,
            'type_period' => $this->type_period,

            'sale' => $this->sale,
            'taxes' => $this->taxes,
            'total_tax' => $this->total_tax,
            'subtotal' => $this->subtotal,
            'total_discount' => $this->total_discount,
        ];
    }

    
    public static function getTransformPayments($payments){
        
        return $payments->transform(function($row, $key){ 
            return [
                'id' => $row->id, 
                'sale_note_id' => $row->sale_note_id, 
                'date_of_payment' => $row->date_of_payment->format('Y-m-d'), 
                'payment_method_type_id' => $row->payment_method_type_id, 
                'has_card' => $row->has_card, 
                'card_brand_id' => $row->card_brand_id, 
                'reference' => $row->reference, 
                'payment' => $row->payment, 
                'payment_method_type' => $row->payment_method_type, 
                'payment_destination_id' => ($row->global_payment) ? ($row->global_payment->type_record == 'cash' ? 'cash':$row->global_payment->destination_id):null, 
                'payment_filename' => ($row->payment_file) ? $row->payment_file->filename:null, 
            ];
        }); 

    }
}
