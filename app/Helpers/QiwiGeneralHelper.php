<?php

namespace App\Helpers;


use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\Services\Qiwi\Qiwi;
use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;


class QiwiGeneralHelper {
    /**
     *  Get qiwi-controlling object from library
     * @param $login
     * @return QIWIControl
     */
    public static function getQiwiControlObject($login) {
        $wallet = QiwiWallet::where("login", $login)->first();
        $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;

        if ($wallet->useProxy) {
            $controlProxy = $proxy['host'] . ":" . $proxy['port'];
            $controlProxyAuth = $proxy['login'] . ":" . $proxy['password'];
            $control = new QIWIControl($login, $wallet->password, "cookie_data", $controlProxy, $controlProxyAuth);
        } else {
            $control = new QIWIControl($login, $wallet->password);
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