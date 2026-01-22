<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
*/

Route::view('/404', 'layouts.404')->name('404');
Route::view('/403', 'layouts.403')->name('403');
Route::view('/error', 'layouts.error_exception')->name('error');

