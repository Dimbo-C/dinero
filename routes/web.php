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

Route::get("/clear", function () {
    while (true) {
        if (Queue::pop() == null) break;
    };
    echo "It is over, it is done";

});

Route::get("/test", function () {
    $login = "+79096127856";
    $name = "Russ 2";
    $password = "F3Eu7F2iaK";
    $useProxy = 1;
    $proxy = [
            "host" => "5.8.61.107",
            "port" => "8239",
            "login" => "user6760",
            "password" => "0ajq7n"
    ];

    //    if (QiwiWallet::walletExistsByName($data->name)) {
    //        $tmpWallet = QiwiWallet::findByName($data->name);
    //        $result['status'] = "failure";
    //        $result['message'] = "Кошелек с таким именем уже есть в системе. Привязан к номеру " . $tmpWallet->login;
    //
    //        return $result;
    //    }
    //    if (QiwiWallet::walletExists($data->login)) {
    //        $tmpWallet = QiwiWallet::findByLogin($data->login);
    //        $result['status'] = "failure";
    //        $result['message'] = "Этот номер уже внесен в систему. Имеет имя " . $tmpWallet->name;
    //
    //        return $result;
    //    }

    //    $request = $data;

    // create new proxy
    //    $proxyRepository = new ProxyRepository();
    //    $proxyRepository->create($request['proxy']);
    //    $proxy = $proxyRepository->getLast();

    echo "Before action: " . date("H:i:s") . " <br>";
    //     try to login to new wallet
    //    $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
    //            $login, $password,
    //            $useProxy, $proxy);
//    $qiwiControl = QiwiGeneralHelper::getQiwiInstance(
//            $login, $password,
//            $useProxy, $proxy
//    );
//    echo "After action: " . date("H:i:s") . " <br>";
//
//    //        die();
//    if ($qiwiControl->getBalance() == false) {
//        echo "FALSEEEE";
//        $result['status'] = "failure";
//        $result['message'] = "Кошелек не найден в системе Qiwi";
//
//        return $result;
//    } else {
//        echo "TRUEEEEE";
//    };

    die();

    // fetch balance from qiwi with library
    $request->typeId = (new QiwiWalletType())->findByType($request->type)->id;
    //                $request->balance = $qiwiControl->loadBalance()['RUB'];
    $request->balance = 0;

    // get income data from qiwi (lib returns empty array for now, so it is a dummy)
    //        $request->monthIncome = $request->balance;
    $request->monthIncome = 0;

    // add new wallet to DB with proxy or not

    $wallet = (new Qiwiwallet())::insertWallet($data, $proxy->id);

    // create a general settings and security settings in DB
    $settings = new QiwiWalletSettings([
            'wallet_id' => $wallet->id,
            "autoWithdrawal_type_id" => 1
    ]);

    $securitySettings = new QiwiWalletSecuritySettings(['wallet_id' => $wallet->id]);
    $wallet->settings()->save($settings);
    $wallet->securitySettings()->save($securitySettings);

    $result['code'] = "200";
    $result['status'] = "success";
    $result['message'] = "Кошелек успешно добавлен.";


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

