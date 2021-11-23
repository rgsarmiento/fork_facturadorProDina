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
use Modules\Sale\Models\SaleOpportunity;
use Modules\Sale\Models\SaleOpportunityItem;
use Modules\Sale\Http\Resources\SaleOpportunityCollection;
use Modules\Sale\Http\Resources\SaleOpportunityResource;
use Modules\Sale\Http\Requests\SaleOpportunityRequest;
use Modules\Factcolombia1\Models\Tenant\{
    Currency,
    Tax,
    PaymentMethod,
    PaymentForm
};

class RemissionController extends Controller
{

    use StorageDocument;

    protected $remission;
    protected $company;


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
        // $customers = $this->table('customers');
        $payment_methods = PaymentMethod::all();
        $payment_forms = PaymentForm::all();
        $currencies = Currency::all();
        $taxes = $this->table('taxes');

        return compact('payment_methods', 'payment_forms', 'currencies', 'taxes');
    }


    // public function option_tables()
    // {
    //     $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
    //     $series = Series::where('establishment_id',$establishment->id)->get();
    //     $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
    //     $payment_method_types = PaymentMethodType::all();
    //     $payment_destinations = []; //$this->getPaymentDestinations();

    //     return compact('series', 'document_types_invoice', 'payment_method_types', 'payment_destinations');
    // }


    public function item_tables()
    {
        $items = $this->table('items');
        $taxes = $this->table('taxes');

        return compact('items', 'taxes');
    }


    public function table($table)
    { 

        if ($table === 'taxes') {
            return Tax::all()->transform(function($row) {
                return $row->getSearchRowResource();
            });
        }

        if ($table === 'items') {

            $items = Item::whereNotItemsAiu()
                            ->whereWarehouse()
                            ->whereIsActive()
                            ->whereNotIsSet()
                            ->orderBy('description')
                            ->take(20)
                            ->get()
                            ->transform(function($row){
                                return $row->getSearchRowResource();
                            });
 
        }

    }


    public function record($id)
    {
        $record = new SaleOpportunityResource(SaleOpportunity::findOrFail($id));

        return $record;
    }
 

    public function store(SaleOpportunityRequest $request) {

        // dd($request->all());

        DB::connection('tenant')->transaction(function () use ($request) {

            $data = $this->mergeData($request);

            $this->sale_opportunity =  SaleOpportunity::updateOrCreate( ['id' => $request->input('id')], $data);

            $this->sale_opportunity->items()->delete();
            $this->sale_opportunity->files()->delete();

            foreach ($data['items'] as $row) {
                $this->sale_opportunity->items()->create($row);
            }


            $this->setFilename();
            $this->createPdf($this->sale_opportunity, "a4", $this->sale_opportunity->filename);

        });

        return [
            'success' => true,
            'data' => [
                'id' => $this->sale_opportunity->id,
            ],
        ];
    }
 

    public function mergeData($inputs)
    {

        $this->company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'external_id' => ($inputs->id) ? $inputs->external_id : Str::uuid()->toString(),
            'customer' => PersonInput::set($inputs['customer_id']),
            'establishment' => EstablishmentInput::set($inputs['establishment_id']),
            'soap_type_id' => $this->company->soap_type_id,
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
    }



    private function setFilename(){

        $name = [$this->sale_opportunity->prefix,$this->sale_opportunity->id,date('Ymd')];
        $this->sale_opportunity->filename = join('-', $name);
        $this->sale_opportunity->save();

    }

 
    public function searchCustomerById($id)
    {

        $customers = Person::whereType('customers')
                    ->where('id',$id)
                    ->get()->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->number.' - '.$row->name,
                            'name' => $row->name,
                            'number' => $row->number,
                            'identity_document_type_id' => $row->identity_document_type_id,
                            'identity_document_type_code' => $row->identity_document_type->code
                        ];
                    });

        return compact('customers');
    }

    public function download($external_id, $format = 'a4') {
        $remission = SaleOpportunity::where('external_id', $external_id)->first();

        if (!$remission) throw new Exception("El código {$external_id} es inválido, no se encontro el pedido relacionado");

        $this->reloadPDF($remission, $format, $remission->filename);

        return $this->downloadStorage($remission->filename, 'sale_opportunity');
    }

    
    public function toPrint($external_id, $format) {
        $remission = SaleOpportunity::where('external_id', $external_id)->first();

        if (!$remission) throw new Exception("El código {$external_id} es inválido, no se encontro el pedido relacionado");

        $this->reloadPDF($remission, $format, $remission->filename);
        $temp = tempnam(sys_get_temp_dir(), 'sale_opportunity');

        file_put_contents($temp, $this->getStorage($remission->filename, 'sale_opportunity'));

        return response()->file($temp);
    }


    private function reloadPDF($remission, $format, $filename) {
        $this->createPdf($remission, $format, $filename);
    }

    public function createPdf($remission = null, $format_pdf = null, $filename = null) {
     
        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $document = ($remission != null) ? $remission : $this->sale_opportunity;
        $company = ($this->company != null) ? $this->company : Company::active();
        $filename = ($filename != null) ? $filename : $this->sale_opportunity->filename;

        $base_template = config('tenant.pdf_template');

        $html = $template->pdf($base_template, "sale_opportunity", $company, $document, $format_pdf);


        $pdf_font_regular = config('tenant.pdf_name_regular');
        $pdf_font_bold = config('tenant.pdf_name_bold');

        if ($pdf_font_regular != false) {
            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $pdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                                DIRECTORY_SEPARATOR.'pdf'.
                                                DIRECTORY_SEPARATOR.$base_template.
                                                DIRECTORY_SEPARATOR.'font')
                ]),
                'fontdata' => $fontData + [
                    'custom_bold' => [
                        'R' => $pdf_font_bold.'.ttf',
                    ],
                    'custom_regular' => [
                        'R' => $pdf_font_regular.'.ttf',
                    ],
                ]
            ]);
        }

        $path_css = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                             DIRECTORY_SEPARATOR.'pdf'.
                                             DIRECTORY_SEPARATOR.$base_template.
                                             DIRECTORY_SEPARATOR.'style.css');

        $stylesheet = file_get_contents($path_css);

        $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        if ($format_pdf != 'ticket') {
            if(config('tenant.pdf_template_footer')) {
                $html_footer = $template->pdfFooter($base_template);
                $pdf->SetHTMLFooter($html_footer);
            }
        }

        $this->uploadFile($filename, $pdf->output('', 'S'), 'sale_opportunity');
    }

    public function uploadFile($filename, $file_content, $file_type) {
        $this->uploadStorage($filename, $file_content, $file_type);
    }

}
