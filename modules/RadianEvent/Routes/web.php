<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {

        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            
            Route::prefix('co-radian-events')->group(function () {

                Route::get('reception', 'RadianEventController@reception')->name('tenant.co-radian-events-reception.index');
                Route::post('upload', 'RadianEventController@upload');

            });

        });
    });
}
