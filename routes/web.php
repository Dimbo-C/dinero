<?php

use App\QiwiWalletType;
use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/aliexpress', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');

Route::get("/test", function () {
    while (true) {
        if (Queue::pop() == null) break;
    };

    echo "It is over, it is done";

    //    $login = "+380960968460";
    //    //    $login = "+380507308340";
    //    $password = "Crixalis2204";
    //    $qiwi = \App\Helpers\QiwiGeneralHelper::getQiwiInstance($login, $password, false);
    //    //    $qiwi->login();
    //    dump($qiwi->getBalance());
    //    dd($qiwi);
    //    echo $res->getBody()->getContents();

    //    $control = \App\Helpers\QiwiGeneralHelper::getQiwiControlObject("380960968460");
    //    dd(['card provider' => $control->detectCardProvider("5168742207673892")]);

    //    dd(json_decode($json));
    //    file_put_contents()
    //    dump()
    //    $money = 361.1;
    //    $percent = 2;
    //    dd(\App\Helpers\MoneyHelper::getBaseCost($money, $percent, 50));
    //    $login = "380960968460";
    //
    //    dispatch(new UpdateBalanceJob($login));
});

Route::get('/{all}', 'HomeController@show')
        ->where('slug', '!=', '/')
        ->where('slug', '!=', 'login')
        ->where('all', '!=', 'logout')
        ->where('all', '!=', 'register')
        ->where(['all' => '.*']);

