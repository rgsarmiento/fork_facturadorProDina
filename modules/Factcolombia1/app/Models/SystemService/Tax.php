<?php

namespace App\Models\SystemService;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesSystemConnection;

class Tax extends Model
{
    use UsesSystemConnection;
  
    protected $table = 'service_taxes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'code',
    ];
}
