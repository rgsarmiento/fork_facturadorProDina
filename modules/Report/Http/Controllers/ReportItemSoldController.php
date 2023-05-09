<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use App\Models\Tenant\{
    DocumentItem,
    DocumentPosItem
};


class ReportItemSoldController extends Controller
{

    public function index()
    {
        return view('report::co-items-sold.index');
    }


    public function getQueryRecords($request)
    {
        $document_type_id = $request->document_type_id ?? null;

        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;
        $start_time = $request->start_time ?? null;
        $end_time = $request->end_time ?? null;
        $user_id = $request->user_id ?? null;
        $customer_id = $request->customer_id ?? null;


        $document_items = DocumentItem::
        with([
            'document' => function($query){
                return $query->select([
                    'id',
                    'prefix',
                    'number'
                ]);
            }
        ])
        ->whereHas('document', function($query) use($start_date, $end_date, $customer_id, $start_time, $end_time, $user_id){

            if($customer_id) $query->where('customer_id', $customer_id);

            if($start_time) $query->where('time_of_issue', '>=', $start_time);

            if($end_time) $query->where('time_of_issue', '<=', $end_time);

            if($user_id) $query->where('user_id', '<=', $user_id);

            return $query->whereBetween('date_of_issue', [$start_date, $end_date]);
        })
        ->get();

        // dd($document_items);

        $document_items_pos = DocumentPosItem::with([
            'document_pos' => function($query){
                return $query->select([
                    'id',
                    'prefix',
                    'number'
                ]);
            }
        ])
        ->whereHas('document_pos', function($query) use($start_date, $end_date, $customer_id, $start_time, $end_time, $user_id){

            if($customer_id) $query->where('customer_id', $customer_id);

            if($start_time) $query->where('time_of_issue', '>=', $start_time);

            if($end_time) $query->where('time_of_issue', '<=', $end_time);

            if($user_id) $query->where('user_id', '<=', $user_id);

            return $query->whereBetween('date_of_issue', [$start_date, $end_date]);
        })
        ->get();

        return $document_items->concat($document_items_pos);
    }


    public function pdf(Request $request) 
    {
        $records = $this->getQueryRecords($request);
        // dd($records, $request->all());

        $company = Company::first();
        $establishment = auth()->user()->establishment;

        $pdf = PDF::loadView('report::co-items-sold.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Reporte_Articulos_Vendidos_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }

}
