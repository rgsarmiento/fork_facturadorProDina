<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::post('/client/configuration/storeServiceCompanieSoftware', 'Tenant\ConfigurationController@storeServiceSoftware');
            Route::post('/client/configuration/storeServiceCompanieResolution', 'Tenant\ConfigurationController@storeServiceResolution');
            Route::post('/client/configuration/storeServiceCompanieCertificate', 'Tenant\ConfigurationController@storeServiceCertificate');

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

            Route::get('/co-configuration', 'Tenant\ConfigurationController@index')->name('tenant.configuration');
            Route::post('/company', 'Tenant\ConfigurationController@company');
        });
    });
}
