<?php

namespace App\Models\TenantService;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class TypeDocumentIdentification extends Model
{
     use UsesTenantConnection;
    protected $table = 'service_type_document_identifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code',
    ];
}