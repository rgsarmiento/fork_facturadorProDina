<?php

namespace Modules\Purchase\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Purchase\Models\{
    SupportDocument  
};
use Modules\Purchase\Http\Resources\{
    SupportDocumentCollection,
    SupportDocumentResource
};
use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
    NoteConcept,
};
use Modules\Purchase\Http\Requests\SupportDocumentRequest;
use Modules\Purchase\Helpers\SupportDocumentHelper;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController;
use Modules\Payroll\Traits\UtilityTrait; 
use Modules\Factcolombia1\Http\Controllers\Tenant\ConfigurationController;


class SupportDocumentAdjustNoteController extends Controller
{

    use UtilityTrait;

    public function create($support_document_id)
    {
        return view('purchase::support_documents.form_adjust_note', compact('support_document_id'));
    }
    
    
    /**
     *
     * @return array
     */
    public function tables()
    {
        $resolutions = TypeDocument::getResolutionsByCode(TypeDocument::DSNOF_ADJUST_NOTE_CODE);
        $currencies = $this->generalTable('currencies');
        $taxes = $this->generalTable('taxes');
        $credit_note_concepts = NoteConcept::where('type_document_id', 3)->get(); //notas credito

        return compact('currencies', 'taxes', 'resolutions', 'credit_note_concepts');
    }

     
    /**
     * @param  int $id
     * @return array
     */
    public function record($id)
    {
        return SupportDocument::getDataAdjustNote($id);
    }

    
    /**
     * 
     * Registrar documento de soporte
     *
     * @param  SupportDocumentRequest $request
     * @return array
     */
    public function store(SupportDocumentRequest $request)
    {
        try 
        {
            $support_document = DB::connection('tenant')->transaction(function () use ($request) {

                $helper = new SupportDocumentHelper();
                $inputs = $helper->getInputs($request);

                $document =  SupportDocument::create($inputs);
                
                foreach ($inputs['items'] as $row)
                {
                    $document->items()->create($row); 
                }

                // enviar documento a la api
                $send_to_api = $helper->sendToApi($document, $inputs);

                $document->update([
                    'response_api' => $send_to_api
                ]);

                return $document;

            });

            return [
                'success' => true,
                'message' => 'Documento de soporte registrado con éxito',
                'data' => [
                    'id' => $support_document->id,
                    'number_full' => $support_document->number_full,
                ],
            ];
            
        } catch (Exception $e)
        {
            return $this->getErrorFromException($e->getMessage(), $e);
        }

    }


}
