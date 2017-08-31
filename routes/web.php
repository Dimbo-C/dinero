<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('get-own-admins', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');
Route::get("/test-qiwi","QiwiWalletsController@");

Route::get('/{all}', 'HomeController@show')
    ->where('slug', '!=', '/')
    ->where('slug', '!=', 'login')
    ->where('all', '!=', 'logout')
    ->where('all', '!=', 'register')
    ->where(['all' => '.*']);
