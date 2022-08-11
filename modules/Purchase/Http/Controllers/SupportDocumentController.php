<?php

namespace Modules\Purchase\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use Illuminate\Support\Str;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use Carbon\Carbon;
use Modules\Purchase\Models\{
    SupportDocument   
};
use Modules\Purchase\Http\Resources\{
    SupportDocumentCollection
};
use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
    Currency,
    PaymentMethod,
    PaymentForm
};
use Modules\Factcolombia1\Models\TenantService\{
    TypeGenerationTransmition
};
use Modules\Purchase\Http\Requests\SupportDocumentRequest;
use Modules\Purchase\Helpers\SupportDocumentHelper;


class SupportDocumentController extends Controller
{

    public function index()
    {
        return view('purchase::support_documents.index');
    }

    public function create()
    {
        return view('purchase::support_documents.form');
    }

    public function columns()
    {
        return [
            'number' => 'Número',
            'date_of_issue' => 'Fecha de emisión',
        ];
    }

    public function records(Request $request)
    {
        $records = SupportDocument::where($request->column, 'like', "%{$request->value}%");

        return new SupportDocumentCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function tables()
    {
        $suppliers = $this->generalTable('suppliers');
        $resolutions = TypeDocument::select('id','prefix', 'resolution_number', 'code')->where('code', TypeDocument::DSNOF_CODE)->get();
        $payment_methods = PaymentMethod::get();
        $payment_forms = PaymentForm::get();
        $currencies = Currency::get();
        $taxes = $this->generalTable('taxes');

        return compact('suppliers','payment_methods','payment_forms','currencies', 'taxes', 'resolutions');
    }

    
    public function item_tables()
    {
        $items = $this->generalTable('items');
        $taxes = $this->generalTable('taxes');
        $type_generation_transmitions = TypeGenerationTransmition::get();

        return compact('items', 'taxes', 'type_generation_transmitions');
    }


    // public function record($id)
    // {

    //     $record = new FixedAssetPurchaseResource(FixedAssetPurchase::findOrFail($id));

    //     return $record;
    // }

    
    /**
     * 
     * Registrar documento de soporte
     *
     * @param  SupportDocumentRequest $request
     * @return array
     */
    public function store(SupportDocumentRequest $request)
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
            // $send_to_api = $helper->sendToApi($document, $inputs);

            // $document->update([
            //     'response_api' => $send_to_api
            // ]);

            return $document;

        });

        return [
            'success' => true,
            'data' => [
                'id' => $support_document->id,
                'number_full' => $support_document->number_full,
            ],
        ];
    }

 

    public static function convert($inputs)
    {
        $company = Company::active();
        $values = [
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'supplier' => PersonInput::set($inputs['supplier_id']),
            'soap_type_id' => $company->soap_type_id,
            'group_id' => ($inputs->document_type_id === '01') ? '01':'02',
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
    }

 

}
