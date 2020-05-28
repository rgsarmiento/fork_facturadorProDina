<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {

            Route::prefix('documents-co')->group(function () {

                Route::get('', 'Tenant\DocumentController@create')->name('tenant.documents-co.create');
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

            Route::prefix('co-companies')->group(function () {

                Route::get('', 'System\HomeController@index')->name('system.co-companies');
                Route::get('tables', 'System\CompanyController@tables');
 
            });

            Route::post('/company', 'System\CompanyController@store')->name('system.company');

        });
    });

}

