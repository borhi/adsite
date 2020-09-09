<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Ad;

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

Route::get('ads', 'AdController@index');
Route::get('ads/{id}', 'AdController@show');
Route::post('ads', 'AdController@store');
