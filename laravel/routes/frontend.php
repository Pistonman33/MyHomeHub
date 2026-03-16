<?php

use Illuminate\Support\Facades\Route;

// SUBDOMAINS
Route::domain(env('BLOG_SUBDOMAIN'))->prefix('/')->name('blog.')
    ->group(function () {
        Route::get('', 'BlogController@front')->name('posts');
        Route::get('{slug}', 'BlogController@post')->name('post');
});

Route::domain(env('MEDIA_SUBDOMAIN'))->prefix('movies')
    ->name('movies.')
    ->group(function () {

        Route::get('/', 'MoviesController@home')->name('index');
        Route::post('filter', 'MoviesController@filter')->name('filter');
    });

Route::domain(env('MEDIA_SUBDOMAIN'))->prefix('tvshows')
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

Route::prefix('friends')
    ->name('friends.')
    ->group(function () {
        Route::get('', 'FriendsController@index')->name('all');
    });

Route::prefix('ctt')
    ->name('ctt.')
    ->group(function () {
        Route::get('/', 'CttController@index')->name('ctt');
    });


Route::prefix('test')
    ->name('test.')
    ->group(function () {
        Route::get('', 'FriendsController@tailwindcss')->name('tailwindcss');
    });    