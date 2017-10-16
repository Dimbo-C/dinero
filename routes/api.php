<?php

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

Route::prefix("qiwi-wallets")->group(function () {
    Route::get('/', 'QiwiWalletsController@all');
    Route::post('/', 'QiwiWalletsController@store');
    Route::post('/update-balance', 'QiwiWalletsController@updateBalance');
    Route::post('/update-income', 'QiwiWalletsController@updateIncome');
    Route::post('/withdraw', 'QiwiWalletsController@withdraw');

    Route::post('/move', 'QiwiWalletsController@move');
    Route::post('/mass-action', 'QiwiWalletsController@massAction');

    Route::get('/{wallet}/report', 'Api\QiwiWalletsController@report');
    Route::get('/{wallet}/settings', 'Api\QiwiWalletsController@settings');

    Route::post('/{wallet}/settings', 'QiwiWalletsController@saveSettings');
    Route::post('/{wallet}/security', 'QiwiWalletsController@security');
    Route::get('/{wallet}/security', 'QiwiWalletsController@fetchSecurity');
    Route::post('/{wallet}/withdraw', 'QiwiWalletsController@withdraw');
    Route::post('/{wallet}/auto-withdraw', 'QiwiWalletsController@autoWithdraw');

    Route::post('/{wallet}/activate-voucher', 'QiwiWalletsController@activateVoucher');
    Route::post('/{wallet}/create-voucher', 'QiwiWalletsController@createVoucher');
    Route::post('/remove/{wallet}', 'QiwiWalletsController@remove');
});