<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/aliexpress', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');
Route::get("/test-qiwi", "QiwiWalletsController@");

Route::get("/test", function () {

    $client = new DeathByCaptcha_SocketClient(env("DBC_USERNAME"), env("DBC_PASSWORD"));
    $client->decode();
    dd($client->get_user());
    dd($client->get_balance());
});

Route::get('/{all}', 'HomeController@show')
        ->where('slug', '!=', '/')
        ->where('slug', '!=', 'login')
        ->where('all', '!=', 'logout')
        ->where('all', '!=', 'register')
        ->where(['all' => '.*']);

