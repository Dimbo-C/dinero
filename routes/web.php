<?php

use App\QiwiWallet;
use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/aliexpress', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');


Route::get("/test", function () {
    $login = "+79096127856";
    $control = \App\Helpers\QiwiGeneralHelper::getQiwiControlObject($login);
    $control->setQIWIWalletOwnerData(
            "Иванович",
            "Иван",
            "Иванович",
            "1998-02-12",
            "4400 111222",
            "",
            "",
            ""
    );
    dump($control->getQIWIWalletOwnerData());
    //    $response = $control->setQIWIWalletOwnerData(
    //            "Сокольников",
    //            "Павел",
    //            "Николаевич",
    //            "1991-12-16",
    //            "0316 197549",
    //            "",
    //            "",
    //            ""
    //    );
    dump($control->getLastError());
    //    dd($response);


    //    dd(QiwiWallet::find(1)->settings);
});

Route::get('/{all}', 'HomeController@show')
        ->where('slug', '!=', '/')
        ->where('slug', '!=', 'login')
        ->where('all', '!=', 'logout')
        ->where('all', '!=', 'register')
        ->where(['all' => '.*']);

