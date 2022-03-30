<?php

namespace Modules\Payroll\Models;

use App\Models\Tenant\ModelTenant;


class DocumentPayrollAdjustNote extends ModelTenant
{

    protected $table = 'co_document_payroll_adjust_notes';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 

        'co_document_payroll_id',
        'type_payroll_adjust_note_id',
        'affected_document_payroll_id',

    ];

    
    /**
     * Relación con nómina de eliminación o reemplazo
     *
     */
    public function payroll() 
    {
        return $this->belongsTo(DocumentPayroll::class, 'co_document_payroll_id');
    }
    
    /**
     * Relación con nómina afectada
     *
     */
    public function affected_document_payroll() 
    {
        return $this->belongsTo(DocumentPayroll::class, 'affected_document_payroll_id');
    }
    
    
    public function type_payroll_adjust_note() 
    {
        return $this->belongsTo(TypePayrollAdjustNote::class, 'type_payroll_adjust_note_id');
    }

}
