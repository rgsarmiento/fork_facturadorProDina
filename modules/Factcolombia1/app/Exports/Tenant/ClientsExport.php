<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Tenant\Client;

class ClientsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Client::all();
    }
}