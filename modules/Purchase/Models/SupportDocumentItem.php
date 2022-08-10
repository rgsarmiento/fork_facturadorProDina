<?php

namespace Modules\Purchase\Models;

use Modules\Factcolombia1\Models\Tenant\TypeUnit;

use App\Models\Tenant\{
    ModelTenant,
    Item,
};

class DocumentItem extends ModelTenant
{

    protected $table = 'co_support_document_items';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'co_support_document_id',
        'item_id',
        'item',
        'unit_type_id',
        'quantity',
        'tax_id',
        'tax',
        'unit_price',
        'total_tax',
        'subtotal',
        'discount',
        'total',
    ];

    
    public function unit_type()
    {
        return $this->belongsTo(TypeUnit::class, 'unit_type_id');
    }

    public function getItemAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setItemAttribute($value)
    {
        $this->attributes['item'] = (is_null($value))?null:json_encode($value);
    }


    public function model_item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    public function support_document()
    {
        return $this->belongsTo(SupportDocument::class, 'co_support_document_id');
    }
    
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    
}