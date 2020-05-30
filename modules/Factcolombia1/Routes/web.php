<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {

            Route::prefix('co-documents')->group(function () {

                Route::get('', 'Tenant\DocumentController@create')->name('tenant.co-documents.create');
                Route::get('search/customers', 'Tenant\DocumentController@searchCustomers');
                Route::get('search/customer/{id}', 'Tenant\DocumentController@searchCustomerById');
                Route::get('tables', 'Tenant\DocumentController@tables');
                Route::post('', 'Tenant\DocumentController@store'); 
                Route::get('item/tables', 'Tenant\DocumentController@item_tables');
                Route::get('table/{table}', 'Tenant\DocumentController@table');
                Route::get('search-items', 'Tenant\DocumentController@searchItems');
                Route::get('search/item/{item}', 'Tenant\DocumentController@searchItemById');
    
            });

            Route::prefix('co-items')->group(function () {
            
                Route::get('', 'Tenant\ItemController@index')->name('tenant.co-items.index');
                Route::get('columns', 'Tenant\ItemController@columns');
                Route::get('records', 'Tenant\ItemController@records');
                Route::get('record/{id}', 'Tenant\ItemController@record');
                Route::get('tables', 'Tenant\ItemController@tables');
                Route::post('', 'Tenant\ItemController@store');
                Route::delete('{item}', 'Tenant\ItemController@destroy');

            });


            Route::post('/countries', 'Tenant\ConfigurationController@countries');
            Route::post('/departments/{country}', 'Tenant\ConfigurationController@departments');
            Route::post('/cities/{department}', 'Tenant\ConfigurationController@cities');


            Route::prefix('co-clients')->group(function () {

                Route::get('', 'Tenant\ClientController@index')->name('tenant.co-clients.index');
                Route::get('columns', 'Tenant\ClientController@columns');
                Route::get('records', 'Tenant\ClientController@records');
                Route::get('record/{id}', 'Tenant\ClientController@record');
                Route::get('tables', 'Tenant\ClientController@tables');
                Route::post('', 'Tenant\ClientController@store');
                Route::put('{client}', 'Tenant\ClientController@update');

                Route::post('/clientsAll', 'Tenant\ClientController@all');
                Route::delete('{client}', 'Tenant\ClientController@destroy');

            });
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

