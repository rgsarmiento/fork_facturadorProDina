<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant; 

class TypePayrollAdjustNote extends ModelTenant
{

    protected $table = 'co_type_payroll_adjust_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code',
    ];

        
    /**
     * Use in resource and collection
     *
     * @return array
     */
    public function getRowResource(){

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
        ];

    }

}
