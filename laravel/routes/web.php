<?php
use App\Record;

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('finance')->group(function () {
  Route::get('','FinanceController@home')->name("finance")->middleware('auth');
  Route::get('import','FinanceController@import')->name("finance.import")->middleware('auth');
  Route::post('import','FinanceController@import')->name("finance.import_post")->middleware('auth');

  Route::get('show','FinanceController@show')->name("finance.show")->middleware('auth');
  Route::get('show/{offset}','FinanceController@show')->name("finance.show_with_offset")->middleware('auth');
  Route::get('delete/{transactionid}','FinanceController@delete')->middleware('auth');
  Route::post('show','FinanceController@show')->name("finance.show_post")->middleware('auth');

  Route::get('all','FinanceController@all')->name("finance.all")->middleware('auth');
  Route::post('search','FinanceController@search')->name("finance.search")->middleware('auth');

  Route::get('update_records',function(){
    dd(Record::assignCategoryRecords());
  });  

});

Route::prefix('stats')->group(function () {
  Route::get('','StatsController@home')->name("stats")->middleware('auth');
  Route::get('category/{id}','StatsController@show')->name("stats.show")->middleware('auth');
});

Route::prefix('library')->middleware('auth')->group(function () {
    Route::get('scan','ScanController@home')->name("library.scan");
    Route::post('/api/barcode', 'ScanController@lookup')->name("library.scan.lookup");
});

/*
Route::prefix('documents')->group(function () {
    Route::get('','DocumentController@home')->name("documents")->middleware('auth');
});
*/
Route::prefix('movies')->group(function () {
    Route::get('','MoviesController@home')->name("movies");
    Route::post('filter','MoviesController@filter')->name("movies.filter");
    Route::get('all','MoviesController@all')->name("movies.all")->middleware('auth');
    Route::get('picture/{id}','MoviesController@picture')->middleware('auth');
    Route::post('search','MoviesController@search')->name("movies.search")->middleware('auth');
    //Route::get('savePicture','MoviesController@savePicture')->middleware('auth');
    // Pending Movie
    Route::get('pending','MoviesController@pending')->name("movies.pending")->middleware('auth');
    Route::get('pending/{offset}','MoviesController@pending')->name("movies.pending_with_offset")->middleware('auth');
    Route::post('pending','MoviesController@pending')->name("movies.pending_post")->middleware('auth');
    Route::get('delete/{movieid}','MoviesController@delete')->name("movies.delete")->middleware('auth');

    Route::get('check/db','MoviesController@checkDBSQlite')->name("movies.checkDBSQlite")->middleware('auth');

});

Route::prefix('tvshows')->group(function () {
    Route::get('','SeriesController@home')->name("tvshows")->middleware('auth');
    Route::post('filter','SeriesController@filter')->name("tvshows.filter");
    // Pending Movie
    Route::get('pending','SeriesController@pending')->name("tvshows.pending")->middleware('auth');
    Route::get('pending/{offset}','SeriesController@pending')->name("tvshows.pending_with_offset")->middleware('auth');
    Route::post('pending','SeriesController@pending')->name("tvshows.pending_post")->middleware('auth');
    Route::get('delete/{movieid}','SeriesController@delete')->name("tvshows.delete")->middleware('auth');

});


Route::prefix('backup')->group(function () {
  Route::get('','BackupController@show')->name("backup")->middleware('auth');
  Route::get('create','BackupController@create')->name("backup.create")->middleware('auth');
  Route::get('download/{file_name}','BackupController@download')->middleware('auth');
  Route::get('restore/{file_name}','BackupController@restore')->middleware('auth');
  Route::get('delete/{file_name}','BackupController@delete')->middleware('auth');
});

Route::get('/404',function(){
  return view('layouts.404');
});
Route::get('/403',function(){
  return view('layouts.403');
});
Route::get('/error',function(){
      return view('layouts.error_exception');
});


