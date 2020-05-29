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

