<?php

namespace Modules\Factcolombia1\Helpers;

use App\Models\Tenant\Document;
use App\Models\Tenant\Person;
use App\Models\Tenant\Item;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use Illuminate\Support\Str;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Factcolombia1\Models\Tenant\{
    Tax,
};

class DocumentHelper{
  
    public static function createDocument($request, $nextConsecutive, $correlative_api, $company, $response, $response_status)
    {

        $establishment = EstablishmentInput::set(auth()->user()->establishment_id);

        $document = Document::create([
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => auth()->user()->establishment_id,
            'establishment' => $establishment,
            'soap_type_id' => Company::active()->soap_type_id,
            'send_server' => false,


            'type_document_id' => $request->type_document_id,
            'prefix' => $nextConsecutive->prefix,
            'number' => $correlative_api,
            'type_invoice_id' => $request->type_invoice_id,
            'customer_id' => $request->customer_id,
            'customer' => Person::with('typePerson', 'typeRegime', 'identity_document_type', 'country', 'department', 'city')->findOrFail($request->customer_id),
            'currency_id' => $request->currency_id,
            // 'date_issue' => Carbon::parse("{$request->date_issue} ".Carbon::now()->format('H:i:s')),
            'date_expiration' => Carbon::parse("{$request->date_expiration}"),
            'date_of_issue' => Carbon::parse($request->date_issue)->format('Y-m-d'),
            'time_of_issue' => Carbon::now()->format('H:i:s'),
            'observation' => $request->observation,
            'reference_id' => $request->reference_id,
            'note_concept_id' => $request->note_concept_id,
            'sale' => $request->sale,
            'total_discount' => $request->total_discount,
            'taxes' => $request->taxes,
            'total_tax' => $request->total_tax,
            'subtotal' => $request->subtotal,
            'total' => $request->total,
            'version_ubl_id' => $company->version_ubl_id,
            'ambient_id' => $company->ambient_id,

            'payment_form_id' =>$request->payment_form_id,
            'payment_method_id' =>$request->payment_method_id,
            'time_days_credit' => $request->time_days_credit,

            'response_api' => $response,
            'response_api_status' => $response_status,
            'correlative_api' => $correlative_api

        ]);

        
        foreach ($request->items as $item) {

            $record_item = Item::find($item['id']);
            
            $json_item = [
                'name' => $record_item->name,
                'description' => $record_item->description,
                'internal_id' => $record_item->internal_id,
                'unit_type' => (key_exists('item', $item))?$item['item']['unit_type']:$record_item->unit_type,
                'unit_type_id' => (key_exists('item', $item))?$item['item']['unit_type_id']:$record_item->unit_type_id,
                'presentation' => (key_exists('item', $item)) ? (isset($item['item']['presentation']) ? $item['item']['presentation']:[]):[],
                'amount_plastic_bag_taxes' => $record_item->amount_plastic_bag_taxes,
                'is_set' => $record_item->is_set,
                'lots' => (isset($item['item']['lots'])) ? $item['item']['lots']:[],
                'IdLoteSelected' => ( isset($item['IdLoteSelected']) ? $item['IdLoteSelected'] : null )
            ];

            $document->items()->create([
                'item_id' => $item['id'],
                'item' => array_merge($item, $json_item),
                'unit_type_id' => $item['unit_type_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'tax_id' => $item['tax_id'],
                'tax' => Tax::find($item['tax_id']),
                'total_tax' => $item['total_tax'],
                'subtotal' => $item['subtotal'],
                'discount' => $item['discount'],
                'total' => $item['total']
            ]);

        }

        return $document;
    }

}