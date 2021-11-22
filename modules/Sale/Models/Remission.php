<?php

namespace Modules\Sale\Models;

use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
    StateDocument,
    DetailDocument,
    Currency,
    PaymentForm,
    PaymentMethod,
};
use App\Models\Tenant\{
    StateType,
    User,
    Establishment,
};

class Remission extends ModelTenant
{

    protected $table = 'co_remissions';

    protected $fillable = [

        'user_id',
        'external_id',
        'establishment_id',
        'establishment',
        'state_type_id',

        'date_of_issue',
        'time_of_issue',

        'customer_id',
        'customer',

        'prefix',
        'number',
        'currency_id',

        'date_expiration',
        'observation',

        'sale',
        'total_tax',
        'taxes',
        'total_discount',
        'subtotal',
        'total',

        'payment_form_id',
        'payment_method_id',
        'time_days_credit',
        'filename',

    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'taxes' => 'object',
    ];


    public function getCurrencyTypeIdAttribute()
    {
        return $this->currency->name;
    }

    public function items() 
    {
        return $this->hasMany(RemissionItem::class);
    }

    public function currency() 
    {
        return $this->belongsTo(Currency::class);
    }

    public function getTaxesCollectAttribute() 
    {
        return collect($this->taxes);
    }

    public function payment_form()
    {
        return $this->belongsTo(PaymentForm::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function getNumberFullAttribute()
    {
        return $this->prefix.'-'.$this->number;
    }

    public function getSeriesAttribute()
    {
        return $this->prefix;
    }

    public function getEstablishmentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setEstablishmentAttribute($value)
    {
        $this->attributes['establishment'] = (is_null($value))?null:json_encode($value);
    }

    public function getCustomerAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setCustomerAttribute($value)
    {
        $this->attributes['customer'] = (is_null($value))?null:json_encode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state_type()
    {
        return $this->belongsTo(StateType::class);
    }

    public function person() 
    {
        return $this->belongsTo(Person::class, 'customer_id');
    }

    public function getCompanyAttribute()
    {
        return Company::first();
    }

    // public function inventory_kardex()
    // {
    //     return $this->morphMany(InventoryKardex::class, 'inventory_kardexable');
    // }

    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('user_id', $user->id) : null;
    }

    public function scopeWhereCurrency($query, $currency_id)
    {
        return $query->where('currency_id', $currency_id);
    }

}
