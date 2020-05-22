<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth'])->group(function () {

            Route::prefix('documents-co')->group(function () {

                Route::get('', 'Tenant\DocumentController@create')->name('tenant.documents-co.create');
                
                

            });

        });
    });
}
