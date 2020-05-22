<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle
};
use App\Models\Tenant\Client;

class ClientsFormatExport implements FromCollection, WithHeadings, WithTitle
{
    public function headings(): array {
        return [
            'Código tipo de persona',
            'Código tipo de régimen',
            'Código tipo de documento de identidad',
            'Número de identificación',
            'Nombre completo',
            'Código de país',
            'Código de departamento',
            'Código de ciudad',
            'Dirección',
            'Teléfono',
            'Correo electrónico'
        ];
    }
    
    public function title(): string {
        return 'Formato';
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Client::query()
            ->select('type_person_id', 'type_regime_id', 'type_identity_document_id', 'identification_number', 'name', 'country_id', 'department_id', 'city_id', 'address', 'phone', 'email')
            ->get()
            ->random(1);
    }
}
