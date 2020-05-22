<?php

namespace App\Models\SystemService;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesSystemConnection;


class TypeOrganization extends Model
{
      use UsesSystemConnection;
  

    protected $table = 'service_type_organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code',
    ];
}
