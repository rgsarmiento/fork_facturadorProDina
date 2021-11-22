<?php

namespace Modules\Factcolombia1\Models\TenantService;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class AdvancedConfiguration extends Model
{

    use  UsesTenantConnection;

    protected $table = 'co_advanced_configuration';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'minimum_salary',
        'transportation_allowance',
    ];

        
    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        return [
            'id' => $this->id,
            'minimum_salary' => $this->minimum_salary,
            'transportation_allowance' => $this->transportation_allowance,
        ];

    }

}
