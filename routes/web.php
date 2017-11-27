<?php

use App\Helpers\QiwiGeneralHelper;
use App\Processors\TransactionProcessor;
use App\QiwiWallet;
use App\QiwiWalletType;
use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/aliexpress', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');

Route::get("/test", function () {
    $login = "+79775286985";

    //    $monthIncome = QiwiGeneralHelper::getMonthIncome($login);
    //    Log::info("Update income job started");
    //    $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
    //    $qiwi->setNewTimeout(40);
    //    $monthIncome = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];
    //
    //    //            $monthIncome = QiwiGeneralHelper::getMonthIncome($this->login);
    //
    //    $wallet = QiwiWallet::findByLogin($login);
    //    $wallet->updateIncome($monthIncome);
    //    Log::info("Update income job income is $monthIncome");
    //    Log::info("Update income job finished");

    //    dd($income);

    //    $query = [
    //            'start' => '22.11.2017',
    //            'end' => '22.11.2017',
    //            'page' => '1',
    //            'size' => '20'
    //    ];
    //
    //    echo "Before action: " . date("H:i:s") . " <br>";
    //
    //    $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
    //    $options = [
    //            'page' => $query['page'],
    //            'size' => $query['size'],
    //    ];
    //
    //    $transactionProcessor = new TransactionProcessor(
    //            $qiwi->reportForDateRange(
    //                    $query['start'],
    //                    $query['end'],
    //                    $options
    //            )
    //    );
    //
    //    dd($transactionProcessor->getTransactions());
    //
    //    echo "After action: " . date("H:i:s") . " <br>";

    while (true) {
        if (Queue::pop() == null) break;
    };

    //
    echo "It is over, it is done";
    //
    die();
    //dd(Queue::getName(Queue::getConnectionName()));
    //    $login = "+380960968460";
    //    $login = "+380507308340";
    //    $password = "Crixalis2204";
    $qiwi = \App\Helpers\QiwiGeneralHelper::getQiwiInstance($login);
    //    $qiwi->login();
    echo "Inside action: " . date("H:i:s") . " <br>";
    dump($qiwi->getBalance());
    //        dd($qiwi);
    echo "After action: " . date("H:i:s") . " <br>";
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

