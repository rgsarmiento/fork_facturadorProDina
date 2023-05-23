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
    public function getData($request)
    {
        $document_type_id = $request->document_type_id ?? null;

        $data = [
            'documents' => [],
            'documents_pos' => [],
            'records' => [],
        ];

        switch ($document_type_id)
        {
            case 'documents':
                $documents = $this->getDocuments($request);
                $data['records'] = $documents;
                $data['documents'] = $documents; 
                break;
            
            case 'documents_pos':
                $documents_pos = $this->getDocumentPos($request);
                $data['records'] = $documents_pos;
                $data['documents_pos'] = $documents_pos; 
                break;

            default:
                $documents = $this->getDocuments($request);
                $documents_pos = $this->getDocumentPos($request);
                $data['records'] = $documents->concat($documents_pos);
                $data['documents'] = $documents;
                $data['documents_pos'] = $documents_pos;
                break;
        }

        return $data;
    }

    
    /**
     *
     * @param  Request $request
     * @return Collection
     */
    private function getDocuments($request)
    {
        return Document::filterReportSalesBook($request)->get();
    }


    /**
     *
     * @param  Request $request
     * @return Collection
     */
    private function getDocumentPos($request)
    {
        return DocumentPos::filterReportSalesBook($request)->get();
    }

    
    /**
     *
     * @param  Collection $documents
     * @return Collection
     */
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
        
        return Tax::whereIn('id', $all_taxes_id->unique())
                    ->withOut(['type_tax'])
                    ->select(['id', 'name', 'code', 'rate', 'conversion', 'is_percentage', 'is_fixed_value', 'is_retention', 'in_base', 'in_tax', 'type_tax_id'])
                    ->orderBy('id')
                    ->get();
    }

        
    /**
     *
     * @param  Request $request
     * @return mixed
     */
    public function export(Request $request) 
    {
        $request['summary_sales_book'] = $request->summary_sales_book === 'true';

        $company = Company::first();
        $establishment = auth()->user()->establishment;
        $filters = $request;
        $data = $this->getData($request);
        $records = $data['records'];
        $taxes = $this->getTaxesDocuments($records);
        $summary_records = $request->summary_sales_book ? $this->getSummaryRecords($data, $request) : [];
        $report_data = compact('records', 'company', 'establishment', 'filters', 'taxes', 'summary_records');

        // dd($summary_records);

        $pdf = PDF::loadView('report::co-sales-book.report_pdf', $report_data)->setPaper('a4', 'landscape');

        $filename = 'Reporte_Libro_Ventas_'.date('YmdHis');

        return $pdf->stream($filename.'.pdf');
    }


    public function getSummaryRecords($data, $request)
    {
        // dd($data);
        $document_type_id = $request->document_type_id ?? null;

        $group_prefix = $data['documents']->groupBy('prefix');
        $summary_records = collect();

        foreach ($group_prefix as $prefix => $documents) 
        {
            $ordered_documents = $documents->sortBy(function ($row) {
                return (int) $row->number;
            });

            $summary_records->push([
                'prefix' => $prefix,
                'ordered_documents' => $ordered_documents,
                'first_document' => $ordered_documents->first(),
                'last_document' => $ordered_documents->last(),
            ]);
        }

        // dd($summary_records);

        // switch ($document_type_id)
        // {
        //     case 'documents':
        //         $records = $this->getDocuments($request);
        //         break;
            
        //     case 'documents_pos':
        //         $records = $this->getDocumentPos($request);
        //         break;

        //     default:
        //         $documents = $this->getDocuments($request);
        //         $documents_pos = $this->getDocumentPos($request);
        //         $records = $documents->concat($documents_pos);
        //         break;
        // }

        return $summary_records;
    }

}
