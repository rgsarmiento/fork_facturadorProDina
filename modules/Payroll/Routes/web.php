<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function() {
 
            Route::prefix('payroll')->group(function () {
 
                Route::prefix('document-payrolls')->group(function () {
                    Route::get('', 'DocumentPayrollController@index')->name('tenant.payroll.document-payrolls.index');
                    Route::get('create', 'DocumentPayrollController@create')->name('tenant.payroll.document-payrolls.create');
                    Route::get('tables', 'DocumentPayrollController@tables');
                    Route::get('columns', 'DocumentPayrollController@columns');
                    Route::get('records', 'DocumentPayrollController@records');
                    Route::get('tables', 'DocumentPayrollController@tables');
                    Route::post('', 'DocumentPayrollController@store');
                    Route::get('record/{record}', 'DocumentPayrollController@record');
                    Route::get('downloadFile/{filename}', 'DocumentPayrollController@downloadFile');
                });


                Route::prefix('workers')->group(function () {
                    Route::get('', 'WorkerController@index')->name('tenant.payroll.workers.index');
                    Route::get('tables', 'WorkerController@tables');
                    Route::get('columns', 'WorkerController@columns');
                    Route::get('records', 'WorkerController@records');
                    Route::get('tables', 'WorkerController@tables');
                    Route::post('', 'WorkerController@store');
                    Route::get('record/{record}', 'WorkerController@record');
                    Route::delete('{record}', 'WorkerController@destroy');
                    Route::get('search', 'WorkerController@searchWorkers');
                    Route::get('search-by-id/{worker}', 'WorkerController@searchWorkerById');
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
