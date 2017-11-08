<?php

namespace App\Helpers;


use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\Services\Qiwi\Qiwi;
use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use Illuminate\Support\Facades\Log;

define("QIWI_VISA_VIRTUAL", "489049");
define("VISA", "4");
define("MASTERCARD", "5");
define("AMERICANEXPRESS", "3");

class QiwiGeneralHelper {
    /**
     * @param string $login
     * @param string $password
     * @param bool $useProxy
     * @param array $proxy
     * @return QIWIControl
     */
    public static function getQiwiControlObject($login, $password = null, $useProxy = false, $proxy = []) {
        if (strpos($login, "+") === false) $login = "+$login";

        if ($password == null) {
            $wallet = QiwiWallet::where("login", $login)->first();
            $useProxy = $wallet->use_proxy;
            $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;
            $password = $wallet->password;
        }

        if ($useProxy) {
            $controlProxy = $proxy['host'] . ":" . $proxy['port'];
            $controlProxyAuth = $proxy['login'] . ":" . $proxy['password'];
            $control = new QIWIControl(
                    $login, $password, "cookie_data",
                    $controlProxy, $controlProxyAuth);
        } else {
            $control = new QIWIControl($login, $password);
        }
        $control->login();

        return $control;
    }

    /**
     *  Get qiwi service object
     * @param $login
     * @return Qiwi
     */
    public static function getQiwiInstance($login) {
        $wallet = QiwiWallet::where("login", $login)->first();
        if ($wallet->use_proxy) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password,
                    $proxy->host . ":" . $proxy->port,
                    $proxy->login . ":" . $proxy->password);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }
        $qiwi->login();

        return $qiwi;
    }

    public static function getTodaysExpenditure($login) {
        $control = self::getQiwiInstance($login);

        return $control->getTotals(date("d.m.Y"), date("d.m.Y"))['expenditure'];
    }

    public static function getBalance($login) {
        $control = self::getQiwiControlObject($login);

        return $control->loadBalance()['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = self::getQiwiInstance($login);
        $income = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];

        return $income;
    }

    public static function detectCardProvider($cardNumber) {
        $sixNum = substr(trim($cardNumber), 0, 6);
        $firstNum = substr(trim($cardNumber), 0, 1);

        if ($sixNum == QIWI_VISA_VIRTUAL) {
            return "VISA_VIRTUAL";
        } elseif ($firstNum == VISA) {
            return "VISA";
        } else {
            return "ELSE";
        }
    }
}