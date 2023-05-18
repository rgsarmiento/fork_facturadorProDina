<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use App\Models\Tenant\{
    Document,
    DocumentPos
};
use DB;
use Modules\Factcolombia1\Models\Tenant\Tax;


class ReportSalesBookController extends Controller
{

    public function index()
    {
        return view('report::co-sales-book.index');
    }

    
    /**
     *
     * @param  Request $request
     * @return Collection
     */
    public function getQueryRecords($request)
    {
        $document_type_id = $request->document_type_id ?? null;
        $records = [];

        $documents = Document::filterInvoiceDocument()
        ->filterByRangeDateOfIssue($request->start_date, $request->end_date)
        ->with([
            'items' => function($items){
                return $items->select([
                    'id',
                    'document_id',
                    'item_id',
                    'quantity',
                    'unit_price',
                    'total',
                    'tax_id',
                    'tax',
                    'total_tax',
                    'subtotal',
                    'discount',
                ]);
            },
            'type_document' => function($type_document){
                return $type_document->select([
                    'id',
                    'name',
                ]);
            },
        ])
        ->select([
            'id',
            'type_document_id',
            'prefix',
            'number',
            'date_of_issue',
            'customer_id',
            'customer',
            'total_discount',
            'total',
            'state_document_id',
            'currency_id',
            'sale',
            // 'taxes',
            'total_tax',
            'subtotal',
        ])
        ->latest()
        ->get();


        return $documents;

        dd($documents);

        // switch ($document_type_id)
        // {
        //     case 'documents':
        //         $records = DocumentItem::filterReportSoldItems($request)->get();
        //         break;
            
        //     case 'documents_pos':
        //         $records = DocumentPosItem::filterReportSoldItems($request)->get();
        //         break;

        //     default:
        //         $document_items = DocumentItem::filterReportSoldItems($request)->get();
        //         $document_items_pos = DocumentPosItem::filterReportSoldItems($request)->get();
        //         $records = $document_items->concat($document_items_pos);

        //         break;
        // }

        return $records;
    }


    public function getTaxesDocuments($documents)
    {
        $all_taxes_id = collect();
        $q = 0;

        foreach ($documents as $document) 
        {
            $document_taxes = $document->items->pluck('tax_id')->toArray();
            $all_taxes_id = $all_taxes_id->merge($document_taxes);

            $q += count($document_taxes);
        }
        
        $taxes = Tax::whereIn('id', $all_taxes_id->unique())
                    ->withOut(['type_tax'])
                    ->select(['id', 'name', 'code', 'rate', 'conversion', 'is_percentage', 'is_fixed_value', 'is_retention', 'in_base', 'in_tax', 'type_tax_id'])
                    ->orderBy('id')
                    ->get();

        // dd($all_taxes_id, $q, $all_taxes_id->unique(), $taxes);

        return $taxes;
    }

        
    /**
     *
     * @param  Request $request
     * @return mixed
     */
    public function export(Request $request) 
    {
        $records = $this->getQueryRecords($request);

        $taxes = $this->getTaxesDocuments($records);
        // dd($documents, $taxes);
        $filters = $request;

        $company = Company::first();
        $establishment = auth()->user()->establishment;

        $pdf = PDF::loadView('report::co-sales-book.report_pdf', compact('records', 'company', 'establishment', 'filters', 'taxes'))->setPaper('a4', 'landscape');

        $filename = 'Reporte_Libro_Ventas_'.date('YmdHis');

        return $pdf->stream($filename.'.pdf');
    }

}
