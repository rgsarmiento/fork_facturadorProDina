<?php

namespace App\Models\Tenant;

use App\Models\Tenant\{
    DocumentPayment,
    SaleNotePayment,
    PurchasePayment,
    DocumentPosPayment,
};
use Modules\Sale\Models\QuotationPayment;
use Modules\Sale\Models\ContractPayment;
use Modules\Finance\Models\IncomePayment;
use Modules\Sale\Models\RemissionPayment;


class PaymentMethodType extends ModelTenant
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'description',
        'has_card',
        'charge',
        'number_days',
        'show_ecommerce',
    ];

    protected $casts = [
        'show_ecommerce' => 'boolean',
    ];

    public function document_payments()
    {
        return $this->hasMany(DocumentPayment::class,  'payment_method_type_id');
    }
    
    // public function sale_note_payments()
    // {
    //     return $this->hasMany(SaleNotePayment::class,  'payment_method_type_id');
    // }
    
    public function purchase_payments()
    {
        return $this->hasMany(PurchasePayment::class,  'payment_method_type_id');
    }

    public function quotation_payments()
    {
        return $this->hasMany(QuotationPayment::class,  'payment_method_type_id');
    }
    
    public function contract_payments()
    {
        return $this->hasMany(ContractPayment::class,  'payment_method_type_id');
    }
    
    public function income_payments()
    {
        return $this->hasMany(IncomePayment::class,  'payment_method_type_id');
    }
    
    public function remission_payments()
    {
        return $this->hasMany(RemissionPayment::class,  'payment_method_type_id');
    }

    public function document_pos_payments()
    {
        return $this->hasMany(DocumentPosPayment::class,  'payment_method_type_id');
    }
    
    /**
     * Mostrar métodos de pago en tienda virtual
     *
     * @param $query
     */
    public function scopeWhereShowInEcommerce($query)
    {
        return $query->where('show_ecommerce', true);
    }

}