<?php

use Illuminate\Support\Facades\Route;

Route::prefix('movies')
    ->name('movies.')
    ->group(function () {

        Route::get('/', 'MoviesController@home')->name('index');
        Route::post('filter', 'MoviesController@filter')->name('filter');
    });

Route::prefix('tvshows')
    ->name('tvshows.')
    ->group(function () {
        Route::get('/', 'SeriesController@home')->name('index');
        Route::post('filter', 'SeriesController@filter')->name('filter');
    });

Route::prefix('library')
    ->name('library.')
    ->group(function () {
      Route::get('scan', 'ScanController@home')->name('scan');
      Route::post('api/barcode', 'ScanController@lookup')->name('scan.lookup');
    });

Route::prefix('blog')
    ->name('blog.')
    ->group(function () {
      Route::get('', 'BlogController@front')->name('posts');
    });
