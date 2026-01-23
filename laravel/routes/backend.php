<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Auth & Home
|--------------------------------------------------------------------------
*/

// ROUTES WITHOUT AUTH MIDDLEWARE FOR AUTHENTICATION
// Login routes
Route::get('login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->withoutMiddleware('auth');

Route::post('login', [LoginController::class, 'login'])
    ->withoutMiddleware('auth');

// Register routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->withoutMiddleware('auth');

Route::post('register', [RegisterController::class, 'register'])
    ->withoutMiddleware('auth');

// Logout route
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/', 'HomeController@index')->name('dashboard');

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
    Route::get('posts/{postId}/edit', 'BlogController@edit')->name('posts.edit');        
});

/*
|--------------------------------------------------------------------------
| Friends
|--------------------------------------------------------------------------
*/
Route::prefix('friends')->name('friends.')->group(function () {
    Route::get('birthdates',  'FriendsController@index')->name('index');
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

