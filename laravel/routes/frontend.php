<?php

use Illuminate\Support\Facades\Route;

// SUBDOMAINS
Route::domain(env('BLOG_SUBDOMAIN'))->prefix('/')->name('blog.')
    ->group(function () {
        Route::get('', 'BlogController@front')->name('posts');
        Route::get('{slug}', 'BlogController@post')->name('post')->where('slug', '[A-Za-z0-9\-]+');
});

Route::domain(env('MEDIA_SUBDOMAIN'))->group(function () {

    Route::get('/', function () {
        return redirect()->route('movies.index');
    });
    Route::prefix('movies')->group(function () {
        Route::get('/', 'MoviesController@home')->name('movies.index');
        Route::post('filter', 'MoviesController@filter')->name('movies.filter');
    });

    Route::prefix('tvshows')->group(function () {
        Route::get('/', 'SeriesController@home')->name('tvshows.index');
        Route::post('filter', 'SeriesController@filter')->name('tvshows.filter');
    });
});

Route::domain(env('MYHOME_SUBDOMAIN'))->group(function () {

    Route::prefix('library')->group(function () {
        Route::get('scan', 'ScanController@home')->name('library.scan');
        Route::post('api/barcode', 'ScanController@lookup')->name('library.scan.lookup');
    });

    Route::prefix('friends')->group(function () {
        Route::get('', 'FriendsController@index')->name('friends.all');
    });

    Route::prefix('ctt')->group(function () {
        Route::get('/', 'CttController@index')->name('ctt');
    });

    Route::prefix('test')->group(function () {
        Route::get('', 'FriendsController@tailwindcss')->name('test.tailwindcss');
    });

});