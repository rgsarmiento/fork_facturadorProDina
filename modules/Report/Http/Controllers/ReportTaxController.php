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
use Modules\Report\Exports\TaxExport;




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
            ->whereBetween('date_of_issue', [
                Carbon::parse($request->date_start)->startOfDay()->format('Y-m-d H:m:s'),
                Carbon::parse($request->date_end)->endOfDay()->format('Y-m-d H:m:s')
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
            'taxTitles' => $taxTitles->values(),
            'taxesAll' => $taxesAll
        ];

    }



    public function excel(Request $request)
    {
        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $taxesAll = collect();

        $documents = Document::query()
            ->with('type_document', 'reference')
            ->whereBetween('date_of_issue', [
                Carbon::parse($request->date_start)->startOfDay()->format('Y-m-d H:m:s'),
                Carbon::parse($request->date_end)->endOfDay()->format('Y-m-d H:m:s')
            ])
            ->get();


        $documents->pluck('taxes')->each(function($taxes) use($taxesAll) {
            collect($taxes)->each(function($tax) use($taxesAll) {
                $taxesAll->push($tax);
            });
        });

        $taxTitles = $taxesAll->unique('id')->values();

        $records = $documents;

        return (new TaxExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->taxitles($taxTitles)
                ->taxesall($taxesAll)
                ->download('Reporte_Impuestos_'.Carbon::now().'.xlsx');

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
