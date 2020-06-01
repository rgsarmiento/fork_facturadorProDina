<?php

namespace Modules\Factcolombia1\Models\Tenant;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    use SoftDeletes, UsesTenantConnection;

    protected $table = 'co_type_documents';
    
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'from' => 'integer',
        'to' => 'integer'
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'template', 'resolution_number', 'resolution_date', 'resolution_date_end', 'technical_key', 'prefix', 'from', 'to'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
