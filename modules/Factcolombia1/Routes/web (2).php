<?php

$currentHostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if ($currentHostname) {
    Route::domain($currentHostname->fqdn)->group(function() {
        Route::get('/login', 'Tenant\LoginController@showLoginForm')->name('login');
        Route::post('/logout', 'Tenant\LoginController@logout')->name('logout');
        Route::post('/login', 'Tenant\LoginController@login');
    });
}
else {
    Route::domain(env('APP_URL_BASE', 'factura'))->group(function() {
        Route::get('/login', 'System\LoginController@showLoginForm')->name('login');
        Route::post('/logout', 'System\LoginController@logout')->name('logout');
        Route::post('/login', 'System\LoginController@login');
    });
}