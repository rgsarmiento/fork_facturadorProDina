<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function() {
 
            Route::prefix('payroll')->group(function () {
 
                Route::prefix('workers')->group(function () {
                    Route::get('', 'WorkerController@index')->name('tenant.payroll.workers.index');
                    Route::get('tables', 'WorkerController@tables');
                    Route::get('columns', 'WorkerController@columns');
                    Route::get('records', 'WorkerController@records');
                    Route::get('tables', 'WorkerController@tables');
                    Route::post('', 'WorkerController@store');
                    Route::get('record/{record}', 'WorkerController@record');
                    Route::delete('{record}', 'WorkerController@destroy');
                });


                // Route::prefix('type-workers')->group(function () {
                //     Route::get('', 'TypeWorkerController@index')->name('tenant.payroll.type-workers.index');
                //     Route::get('columns', 'TypeWorkerController@columns');
                //     Route::get('records', 'TypeWorkerController@records');
                //     Route::get('tables', 'TypeWorkerController@tables');
                //     Route::post('', 'TypeWorkerController@store');
                //     Route::get('record/{record}', 'TypeWorkerController@record');
                //     Route::delete('{record}', 'TypeWorkerController@destroy');
                // });

            }); 

        });
    });
}
