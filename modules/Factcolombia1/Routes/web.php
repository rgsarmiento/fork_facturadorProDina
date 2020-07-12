<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::post('/client/configuration/storeServiceCompanieSoftware', 'Tenant\ConfigurationController@storeServiceSoftware');
            Route::post('/client/configuration/storeServiceCompanieResolution', 'Tenant\ConfigurationController@storeServiceResolution');
            Route::post('/client/configuration/storeServiceCompanieCertificate', 'Tenant\ConfigurationController@storeServiceCertificate');

            Route::prefix('co-documents')->group(function () {

                Route::get('', 'Tenant\DocumentController@index')->name('tenant.co-documents.index');
                Route::get('records', 'Tenant\DocumentController@records');
                Route::get('note/{id}', 'Tenant\DocumentController@note');
                Route::get('record/{id}', 'Tenant\DocumentController@record');
                Route::get('columns', 'Tenant\DocumentController@columns');
                Route::get('create', 'Tenant\DocumentController@create')->name('tenant.co-documents.create');
                Route::get('search/customers', 'Tenant\DocumentController@searchCustomers');
                Route::get('search/customer/{id}', 'Tenant\DocumentController@searchCustomerById');
                Route::get('tables', 'Tenant\DocumentController@tables');
                Route::post('', 'Tenant\DocumentController@store');
                Route::get('item/tables', 'Tenant\DocumentController@item_tables');
                Route::get('table/{table}', 'Tenant\DocumentController@table');
                Route::get('search-items', 'Tenant\DocumentController@searchItems');
                Route::get('search/item/{item}', 'Tenant\DocumentController@searchItemById');
                Route::get('download/{type}/{document}', 'Tenant\DocumentController@download');
                Route::post('sendEmail', 'Tenant\DocumentController@sendEmailCoDocument');
                Route::post('note', 'Tenant\DocumentController@storeNote');
                Route::get('documents/search/externalId/{external_id}', 'Tenant\DocumentController@searchExternalId');


            });

            Route::prefix('co-items')->group(function () {
                Route::get('/items', 'Tenant\ItemController@index')->name('tenant.co-items.items');
                Route::get('', 'Tenant\ItemController@index')->name('tenant.co-items.index');
                Route::get('columns', 'Tenant\ItemController@columns');
                Route::get('records', 'Tenant\ItemController@records');
                Route::get('record/{id}', 'Tenant\ItemController@record');
                Route::get('tables', 'Tenant\ItemController@tables');
                Route::post('', 'Tenant\ItemController@store');
                Route::delete('{item}', 'Tenant\ItemController@destroy');
                Route::get('/items/formatImport', 'Tenant\ItemController@formatImport')->name('tenant.items.import');
                Route::put('/items/import/excel', 'Tenant\ItemController@import');
                Route::get('/items/export', 'Tenant\ItemController@export')->name('tenant.items.export');

            });


            Route::post('/countries', 'Tenant\ConfigurationController@countries');
            Route::post('/departments/{country}', 'Tenant\ConfigurationController@departments');
            Route::post('/cities/{department}', 'Tenant\ConfigurationController@cities');
            Route::post('/concepts/{type_document}', 'Tenant\ConfigurationController@concepts');


            Route::prefix('co-clients')->group(function () {
                Route::get('/clients', 'Tenant\ClientController@index')->name('tenant.co-clients.clients');
                Route::get('', 'Tenant\ClientController@index')->name('tenant.co-clients.index');
                Route::get('columns', 'Tenant\ClientController@columns');
                Route::get('records', 'Tenant\ClientController@records');
                Route::get('record/{id}', 'Tenant\ClientController@record');
                Route::get('tables', 'Tenant\ClientController@tables');
                Route::post('', 'Tenant\ClientController@store');
                Route::put('{client}', 'Tenant\ClientController@update');
                Route::post('/clientsAll', 'Tenant\ClientController@all');
                Route::delete('{client}', 'Tenant\ClientController@destroy');
                Route::get('/clients/export', 'Tenant\ClientController@export')->name('tenant.clients.export');
                Route::get('/clients/formatImport', 'Tenant\ClientController@formatImport');
                Route::put('/clients/import/excel', 'Tenant\ClientController@import');

            });


            Route::prefix('co-taxes')->group(function () {
                Route::get('/taxes', 'Tenant\TaxController@index')->name('tenant.co-taxes.taxes');
                Route::get('', 'Tenant\TaxController@index')->name('tenant.co-taxes.index');
                Route::get('columns', 'Tenant\TaxController@columns');
                Route::get('records', 'Tenant\TaxController@records');
                Route::get('record/{id}', 'Tenant\TaxController@record');
                Route::get('tables', 'Tenant\TaxController@tables');
                Route::post('', 'Tenant\TaxController@store');
                Route::put('{tax}', 'Tenant\TaxController@update');
//                Route::post('/taxesAll', 'Tenant\TaxController@all');
                Route::delete('{tax}', 'Tenant\TaxController@destroy');
                Route::get('/taxes/export', 'Tenant\TaxController@export')->name('tenant.taxes.export');

            });

            Route::get('/co-configuration', 'Tenant\ConfigurationController@index')->name('tenant.configuration');
            Route::post('/company', 'Tenant\ConfigurationController@company');
        });
    });


} else {

    Route::domain(config('tenant.app_url_base'))->group(function() {

        Route::middleware('auth:admin')->group(function() {

            Route::get('/', function () {
                return redirect()->route('system.co-companies');
            });

            Route::prefix('co-companies')->group(function () {

                Route::get('', 'System\HomeController@index')->name('system.co-companies');
                Route::get('tables', 'System\CompanyController@tables');
                Route::post('', 'System\CompanyController@store')->name('system.company');
                Route::post('update', 'System\CompanyController@update');
                Route::get('records', 'System\CompanyController@records');
                Route::get('record/{id}', 'System\CompanyController@record');
                Route::delete('{company}', 'System\CompanyController@destroy');

            });


        });
    });

}

