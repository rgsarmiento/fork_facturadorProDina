<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Item;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use Illuminate\Support\Str;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;
use Modules\Sale\Models\Remission;
use Modules\Sale\Http\Resources\RemissionCollection;
use Modules\Sale\Http\Resources\RemissionResource;
use Modules\Sale\Http\Requests\RemissionRequest;
use Modules\Factcolombia1\Models\Tenant\{
    Currency,
    Tax,
    PaymentMethod,
    PaymentForm
};
use App\Http\Controllers\Tenant\{
    PersonController,
    ItemController,
};
use Modules\Factcolombia1\Helpers\DocumentHelper;


class RemissionController extends Controller
{

    use StorageDocument;

    protected $remission;

    public function index()
    {
        return view('sale::co-remissions.index');
    }

    public function create($id = null)
    {
        return view('sale::co-remissions.form', compact('id'));
    }
 
    public function columns()
    {
        return [
            'date_of_issue' => 'Fecha de emisión',
            'number' => 'Número'
        ];
    }

    public function records(Request $request)
    {

        $records = Remission::where($request->column, 'like', "%{$request->value}%")
                                ->latest();

        return new RemissionCollection($records->paginate(config('tenant.items_per_page')));
    }

    
    public function tables() 
    {
        $customers = $this->table('customers');
        $payment_methods = PaymentMethod::all();
        $payment_forms = PaymentForm::all();
        $currencies = Currency::all();
        $taxes = $this->table('taxes');

        return compact('customers', 'payment_methods', 'payment_forms', 'currencies', 'taxes');
    }


    public function item_tables()
    {
        $items = $this->table('items');
        $taxes = $this->table('taxes');

        return compact('items', 'taxes');
    }


    public function table($table)
    { 

        if ($table === 'customers') {
            $persons = app(PersonController::class)->searchCustomers(new Request());
            return $persons['customers'];
        }

        if ($table === 'taxes') {
            return Tax::all()->transform(function($row) {
                return $row->getSearchRowResource();
            });
        }

        if ($table === 'items') {
            $items = app(ItemController::class)->searchItems(new Request());
            return $items['items'];
        }

    }


    public function record($id)
    {
        $record = new RemissionResource(Remission::findOrFail($id));

        return $record;
    }
 

    public function store(RemissionRequest $request) {

        DB::connection('tenant')->transaction(function () use ($request) {

            $data = $this->mergeData($request);

            $this->remission =  Remission::updateOrCreate( ['id' => $request->input('id')], $data);

            $this->remission->items()->delete();

            foreach ($data['items'] as $row) {
                $this->remission->items()->create($row);
            }

            $this->setFilename();

        });

        return [
            'success' => true,
            'data' => [
                'id' => $this->remission->id,
            ],
        ];
    }
 

    public function mergeData($inputs)
    {

        $establishment_id = auth()->user()->establishment_id;
        $items = DocumentHelper::getDataItemFromInputs($inputs);
        // dd($items);

        $values = [
            'user_id' => auth()->id(),
            'external_id' => ($inputs->id) ? $inputs->external_id : Str::uuid()->toString(),
            'customer' => Person::with('typePerson', 'typeRegime', 'identity_document_type', 'country', 'department', 'city')->findOrFail($inputs['customer_id']),
            'establishment' => EstablishmentInput::set($establishment_id),
            'establishment_id' => $establishment_id,
            'state_type_id' => '01',
            'number' => $this->getNumber(),
            'items' => $items,
        ];

        $inputs->merge($values);

        return $inputs->all();
    }
    
    /**
     * Obtener ultimo numero correlativo
     *
     * @return int
     */
    private function getNumber()
    {
        $remission = Remission::select('number')->latest()->first();

        return ($remission) ? (int) $remission->number + 1 : 1;
    }


    private function setFilename(){

        $name = [$this->remission->prefix,$this->remission->id,date('Ymd')];
        $this->remission->filename = join('-', $name);
        $this->remission->save();

    }

}
