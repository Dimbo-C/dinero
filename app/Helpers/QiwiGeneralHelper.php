<?php

namespace App\Helpers;


use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\Services\Qiwi\Qiwi;
use Illuminate\Support\Facades\Log;
use QIWIControl;

class QiwiGeneralHelper {

    /**
     *  Get qiwi-controlling object from library
     * @param $login
     * @param $password
     * @param $useProxy
     * @param $proxy array|object with host,port,login and password
     * @return \QIWIControl
     */
    public static function getQiwiControlObject($login, $password, $useProxy, $proxy = []) {
        //        if (is_object($proxy)) $proxy = (array) $proxy;
        if ($useProxy) {
            $controlProxy = $proxy['host'] . ":" . $proxy['port'];
            $controlProxyAuth = $proxy['login'] . ":" . $proxy['password'];
            $control = new QIWIControl($login, $password, "cookie_data", $controlProxy, $controlProxyAuth);
        } else {
            $control = new QIWIControl($login, $password);
        }

        return $control;
    }

    public static function getBalance($login, $password, $useProxy, $proxy) {
        $control = QiwiGeneralHelper::getQiwiControlObject($login, $password, $useProxy, $proxy);
        $control->login();

        return $control->loadBalance()['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $tp = new TransactionProcessor($qiwi->reportForDateRange(date("01.m.Y"), date("d.m.Y")));

        return $tp->getIncome();
    }


    /**
     *  Get qiwi service object
     * @param $login
     * @return Qiwi
     */
    public static function getQiwiInstance($login) {
        $wallet = QiwiWallet::where("login", $login)->first();

        //        if (is_array($wallet)) $wallet = (object) $wallet;

        if ($wallet->use_proxy) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password, $proxy->host . ":" . $proxy->port);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }

        return $qiwi;
    }
}