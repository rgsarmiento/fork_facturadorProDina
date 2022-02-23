<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\DocumentExport;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportDocumentTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\DocumentPos;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use App\Http\Resources\Tenant\DocumentPosCollection;
use Modules\Factcolombia1\Models\Tenant\{
    TypeDocument,
};


class ReportDocumentPosController extends Controller
{
    use ReportDocumentTrait;


    public function filter() {

        $document_types = [];
        $persons = $this->getPersons('customers');
        $sellers = $this->getSellers();
        $establishments = $this->getTransformEstablishments();

        return compact('document_types','establishments','persons', 'sellers');
    }


    public function index() {

        return view('report::document_pos.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request->all(), DocumentPos::class);

        return new DocumentPosCollection($records->paginate(config('tenant.items_per_page')));
    }


}
