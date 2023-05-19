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
        $filter_summary_sales_book = $request->has('filter_summary_sales_book') && (bool) $request->filter_summary_sales_book;
        $records = [];

        switch ($document_type_id)
        {
            case 'documents':
                $records = Document::filterReportSalesBook($request)->get();
                break;
            
            case 'documents_pos':
                $records = DocumentPos::filterReportSalesBook($request)->get();
                break;

            default:
                $documents = Document::filterReportSalesBook($request)->get();
                $documents_pos = DocumentPos::filterReportSalesBook($request)->get();
                $records = $documents->concat($documents_pos);
                break;
        }

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
