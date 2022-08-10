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
        $resolutions = TypeDocument::select('id','prefix', 'resolution_number')->where('code', TypeDocument::DSNOF_CODE)->get();
        $payment_methods = PaymentMethod::get();
        $payment_forms = PaymentForm::get();
        $currencies = Currency::get();
        $taxes = $this->generalTable('taxes');

        return compact('suppliers','payment_methods','payment_forms','currencies', 'taxes', 'resolutions');
    }

    

    public function item_tables()
    {

        $fixed_asset_items = $this->table('fixed_asset_items');
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $system_isc_types = SystemIscType::whereActive()->get();
        $price_types = PriceType::whereActive()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $attribute_types = AttributeType::whereActive()->orderByDescription()->get();

        return compact('fixed_asset_items', 'affectation_igv_types', 'system_isc_types', 'price_types',
                        'discount_types', 'charge_types', 'attribute_types');
    }

    

    public function record($id)
    {

        $record = new FixedAssetPurchaseResource(FixedAssetPurchase::findOrFail($id));

        return $record;
    }


    public function store(FixedAssetPurchaseRequest $request)
    {
 
        $data = self::convert($request);

        $purchase = DB::connection('tenant')->transaction(function () use ($data, $request) {

            $doc =  FixedAssetPurchase::updateOrCreate( ['id' => $request->input('id')], $data);
            $doc->items()->delete();
            
            foreach ($data['items'] as $row)
            {
                $doc->items()->create($row); 
            }

            return $doc;

        });

        return [
            'success' => true,
            'data' => [
                'id' => $purchase->id,
                'number_full' => $purchase->number_full,
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
