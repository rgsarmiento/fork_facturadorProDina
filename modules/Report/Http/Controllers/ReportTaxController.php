<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\OrderNoteConsolidatedCollection;
use App\Models\Tenant\Document;




class ReportTaxController extends Controller
{


    public function index() {

        return view('report::tax.index');
    }

    public function records(Request $request)
    {
        $taxesAll = collect();

        $documents = Document::query()
            ->with('type_document', 'reference')
            ->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay()->format('Y-m-d H:m:s'),
                Carbon::parse($request->date_up)->endOfDay()->format('Y-m-d H:m:s')
            ])
            ->get();

        $documents->pluck('taxes')->each(function($taxes) use($taxesAll) {
            collect($taxes)->each(function($tax) use($taxesAll) {
                $taxesAll->push($tax);
            });
        });

        $taxTitles = $taxesAll->unique('id');

        return [
            'success' => true,
            'data' => $documents,
            'taxTitles' => $taxTitles,
            'taxesAll' => $taxesAll
        ];

    }




    public function excel(Request $request)
    {
        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        /**
         * to do
         * crear una funcion para obtener records
         */
        $taxesAll = collect();
        $documents = Document::query()
            ->with('type_document', 'reference')
            ->whereBetween('created_at', [
                Carbon::parse($request->date_from)->startOfDay()->format('Y-m-d H:m:s'),
                Carbon::parse($request->date_up)->endOfDay()->format('Y-m-d H:m:s')
            ])
            ->get();

        $documents->pluck('taxes')->each(function($taxes) use($taxesAll) {
            collect($taxes)->each(function($tax) use($taxesAll) {
                $taxesAll->push($tax);
            });
        });

        $records = $documents;
       // $records = $this->getRecordsOrderNotes($request->all(), OrderNoteItem::class)->get();
        //$params = $request->all();

        $pdf = PDF::loadView('report::tax.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Reporte_Consolidado_Items_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }

    /*public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecordsOrderNotes($request->all(), OrderNoteItem::class)->get();
        $params = $request->all();

        $pdf = PDF::loadView('report::order_notes_consolidated.report_pdf', compact("records", "company", "establishment", "params"));

        $filename = 'Reporte_Consolidado_Items_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }*/


}
