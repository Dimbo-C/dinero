<?php

use Illuminate\Http\Request;

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

Route::get('/proxies', 'ProxiesController@get');
Route::post('/proxies', 'ProxiesController@store');

Route::get('/qiwi-wallets', 'QiwiWalletsController@all');
Route::post('/qiwi-wallets', 'QiwiWalletsController@store');
Route::post('/qiwi-wallets/update', 'QiwiWalletsController@update');

Route::post('/qiwi-wallets/move', 'QiwiWalletsController@move');

Route::get('/qiwi-wallets/{wallet}/report', 'Api\QiwiWalletsController@report');
