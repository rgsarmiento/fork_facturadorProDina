<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Tenant\Tax;

class TaxesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Tax::all();
    }
}
