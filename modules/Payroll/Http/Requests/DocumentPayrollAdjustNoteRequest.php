<?php

namespace Modules\Payroll\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentPayrollAdjustNoteRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ($this->type_payroll_adjust_note_id === 2) ? $this->getValidationsPayrollElimination() : $this->getValidationsPayrollReplace();
    }
    
    
    /**
     * Validaciones para nómina de eliminación
     *
     * @return array
     */
    private function getValidationsPayrollElimination()
    {
        return [ 
            'type_document_id' => [
                'required',
            ],
            'prefix' => [
                'required',
            ],
            'resolution_number' => [
                'required',
            ],
            'type_payroll_adjust_note_id' => [
                'required',
            ],
            'document_payroll_id' => [
                'required',
            ],
        ];
    }

    
    /**
     * Validaciones para nómina de ajuste
     *
     * @return array
     */
    private function getValidationsPayrollReplace()
    {
        return [ 

        ];
    }

}