<?php

namespace App\Models\Tenant;

use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Models\PaymentFile;

class DocumentPosPayment extends ModelTenant
{
    protected $with = ['payment_method_type', 'card_brand'];
    public $timestamps = false;

    protected $table = 'documents_pos_payments';


    protected $fillable = [
        'document_pos_id',
        'date_of_payment',
        'payment_method_type_id',
        'has_card',
        'card_brand_id',
        'reference',
        'change',
        'payment',
    ];

    protected $casts = [
        'date_of_payment' => 'date',
    ];

    public function payment_method_type()
    {
        return $this->belongsTo(PaymentMethodType::class);
    }

    public function card_brand()
    {
        return $this->belongsTo(CardBrand::class);
    }

    public function global_payment()
    {
        return $this->morphOne(GlobalPayment::class, 'payment');
    }

    public function associated_record_payment()
    {
        return $this->belongsTo(DocumentPos::class, 'document_pos_id');
    }

    public function payment_file()
    {
        return $this->morphOne(PaymentFile::class, 'payment');
    }

}
