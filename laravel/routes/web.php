<?php

use Illuminate\Support\Facades\Route;
use App\Record;
use App\Http\Livewire\Posts\PostList;
use App\Http\Livewire\Posts\PostForm;

/*
|--------------------------------------------------------------------------
| Auth & Home
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| =====================
| FRONTEND (public)
| =====================
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| =====================
| ADMIN (auth required)
| =====================
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Finance
        |--------------------------------------------------------------------------
        */
        Route::prefix('finance')->name('finance.')->group(function () {

            Route::get('/', 'FinanceController@home')->name('index');

            Route::get('import', 'FinanceController@import')->name('import');
            Route::post('import', 'FinanceController@import')->name('import.post');

            Route::get('show', 'FinanceController@show')->name('show');
            Route::get('show/{offset}', 'FinanceController@show')->name('show.offset');
            Route::post('show', 'FinanceController@show')->name('show.post');

            Route::get('all', 'FinanceController@all')->name('all');
            Route::post('search', 'FinanceController@search')->name('search');

            Route::get('delete/{transactionid}', 'FinanceController@delete')
                ->name('delete');

            // Debug / maintenance
            Route::get('update-records', function () {
                dd(Record::assignCategoryRecords());
            })->name('update-records');
        });

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */
        Route::prefix('stats')->name('stats.')->group(function () {

            Route::get('/', 'StatsController@home')->name('index');
            Route::get('category/{id}', 'StatsController@show')->name('show');
        });


        /*
        |--------------------------------------------------------------------------
        | Blog / Posts
        |--------------------------------------------------------------------------
        */
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('posts',  'BlogController@index')->name('posts');
            Route::get('posts/create', 'BlogController@create')->name('posts.create');
            Route::get('posts/{article}/edit', 'BlogController@edit')->name('posts.edit');        
        });


        /*
        |--------------------------------------------------------------------------
        | Movies (backend)
        |--------------------------------------------------------------------------
        */
        Route::prefix('movies')->name('movies.')->group(function () {

            Route::get('all', 'MoviesController@all')->name('all');
            Route::post('search', 'MoviesController@search')->name('search');

            Route::get('picture/{id}', 'MoviesController@picture')->name('picture');

            Route::get('pending', 'MoviesController@pending')->name('pending');
            Route::get('pending/{offset}', 'MoviesController@pending')->name('pending.offset');
            Route::post('pending', 'MoviesController@pending')->name('pending.post');

            Route::get('delete/{movieid}', 'MoviesController@delete')->name('delete');

            Route::get('check/db', 'MoviesController@checkDBSQlite')
                ->name('check-db');
        });


        /*
        |--------------------------------------------------------------------------
        | TV Shows
        |--------------------------------------------------------------------------
        */
        Route::prefix('tvshows')->name('tvshows.')->group(function () {
            Route::get('pending', 'SeriesController@pending')->name('pending');
            Route::get('pending/{offset}', 'SeriesController@pending')->name('pending.offset');
            Route::post('pending', 'SeriesController@pending')->name('pending.post');

            Route::get('delete/{movieid}', 'SeriesController@delete')->name('delete');
        });


        /*
        |--------------------------------------------------------------------------
        | Backup
        |--------------------------------------------------------------------------
        */
        Route::prefix('backup')->name('backup.')->group(function () {

            Route::get('/', 'BackupController@show')->name('index');
            Route::get('create', 'BackupController@create')->name('create');
            Route::get('download/{file_name}', 'BackupController@download')->name('download');
            Route::get('restore/{file_name}', 'BackupController@restore')->name('restore');
            Route::get('delete/{file_name}', 'BackupController@delete')->name('delete');
        });

        /*
        Route::prefix('documents')->group(function () {
            Route::get('','DocumentController@home')->name("documents")->middleware('auth');
        });
        */

    });


/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
*/

Route::view('/404', 'layouts.404')->name('404');
Route::view('/403', 'layouts.403')->name('403');
Route::view('/error', 'layouts.error_exception')->name('error');

