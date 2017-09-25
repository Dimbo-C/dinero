<?php

namespace App\Helpers;


use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\Services\Qiwi\Qiwi;
use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use Illuminate\Support\Facades\Log;


class QiwiGeneralHelper {
    /**
     * @param $login
     * @param string $password
     * @param bool $useProxy
     * @param array $proxy
     * @return QIWIControl
     */
    public static function getQiwiControlObject($login, $password = "", $useProxy = false, $proxy = []) {
        if ($password == "") {
            $wallet = QiwiWallet::where("login", $login)->first();
            $useProxy = $wallet->use_proxy;
            $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;
            $password = $wallet->password;
        }

        if ($useProxy) {
            $controlProxy = $proxy['host'] . ":" . $proxy['port'];
            $controlProxyAuth = $proxy['login'] . ":" . $proxy['password'];
            $control = new QIWIControl($login, $password, "cookie_data", $controlProxy, $controlProxyAuth);
        } else {
            $control = new QIWIControl($login, $password);
        }
        $control->login();

        return $control;
    }


    public static function getBalance($login) {
        $control = QiwiGeneralHelper::getQiwiControlObject($login);

        return $control->loadBalance()['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $tp = new TransactionProcessor($qiwi->reportForDateRange(date("01.m.Y"), date("d.m.Y")));
        Log::info("After getmonthincome");

        return $tp->getIncome();
    }


    /**
     *  Get qiwi service object
     * @param $login
     * @return Qiwi
     */
    public static function getQiwiInstance($login) {
        $wallet = QiwiWallet::where("login", $login)->first();
        Log::info("Before");
        if ($wallet->use_proxy) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password, $proxy->host . ":" . $proxy->port);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }
        $qiwi->login();

        Log::info("after qiwi instance");

        return $qiwi;
    }
}