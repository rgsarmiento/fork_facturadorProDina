<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use SoftDeletes, UsesTenantConnection;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'type_unit_id', 'price', 'tax_id'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
    * Get the type type unit belongs to
    */
    public function typeUnit() {
        return $this->belongsTo(TypeUnit::class);
    }
    
    /**
    * Get the tax belongs to
    */
    public function tax() {
        return $this->belongsTo(Tax::class);
    }
}
