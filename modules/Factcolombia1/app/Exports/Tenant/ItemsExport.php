<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Tenant\Item;

class ItemsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Item::all();
    }
}
