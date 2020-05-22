<?php

use Illuminate\Database\Seeder;
use App\Models\Tenant\Tax;


class DataMasterTenantSeeder extends Seeder
{
    //este seeder trabajara igual al jon comentado al crear la companie
    public $tries = 1;
    public $prefix = 'csv';

      /**
     * Tables
     * @var array
     */
    public $tables = [
        'countries' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'countries.csv',
            'columns' => 'id, code, name, code_phone, @created_at, @updated_at'
        ],
        'departments' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'departments.csv',
            'columns' => 'id, name, country_id, @created_at, @updated_at'
        ],
        'cities' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'cities.csv',
            'columns' => 'id, name, department_id, @created_at, @updated_at'
        ],
        'currencies' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'currencies.csv',
            'columns' => 'id, code, name, symbol, @created_at, @updated_at'
        ],
        'type_identity_documents' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_identity_documents.csv',
            'columns' => 'id, code, name, @created_at, @updated_at'
        ],
        'type_regimes' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_regimes.csv',
            'columns' => 'id, code, name, @created_at, @updated_at'
        ],
        'type_obligations' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_obligations.csv',
            'columns' => 'id, code, name, @created_at, @updated_at'
        ],
        'taxes' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'taxes.csv',
            'columns' => 'id, name, code, rate, conversion, is_percentage, is_fixed_value, is_retention, in_base, @created_at, @updated_at'
        ],
        'version_ubls' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'version_ubls.csv',
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'type_people' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_people.csv',
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'type_units' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_units.csv',
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'ambients' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'ambients.csv',
            'columns' => 'id, name, url, @created_at, @updated_at'
        ],
        'type_invoices' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_invoices.csv',
            'columns' => 'id, name, code, @created_at, @updated_at'
        ],
        'type_documents' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'type_documents.csv',
            'columns' => 'id, name, code, template, @created_at, @updated_at'
        ],
        'state_documents' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'state_documents.csv',
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'state_quotes' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'state_quotes.csv',
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'note_concepts' => [
            'file' => 'csv'.DIRECTORY_SEPARATOR.'note_concepts.csv',
            'columns' => 'id, type_document_id, name, code, @created_at, @updated_at'
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tables as $key => $table) {
            DB::connection('tenant')
                ->getpdo()
               ->exec("LOAD DATA LOCAL INFILE '".str_replace(DIRECTORY_SEPARATOR, '/', public_path($this->prefix.DIRECTORY_SEPARATOR."{$key}.{$this->prefix}"))."' INTO TABLE $key({$table['columns']}) SET created_at = NOW(), updated_at = NOW()");
        }

        $tax = Tax::find(6);
        $tax->in_tax = 1;
        $tax->save();
    }
}
