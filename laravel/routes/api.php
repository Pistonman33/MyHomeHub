<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrpcController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('player-result', [GrpcController::class, 'getPlayerResult']);

Route::group(['middleware' => 'auth:api'], function() {
     Route::post('movies', 'MoviesController@saveMovie');
     Route::post('series', 'SeriesController@saveSerie');
});