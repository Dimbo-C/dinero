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
     * @param $login
     * @param null $password
     * @param null $useProxy
     * @param array $proxy
     * @return Qiwi
     */
    public static function getQiwiInstance($login, $password = null, $useProxy = null, $proxy = []) {
        $wallet = self::getQiwiInstanceWallet($login, $password, $useProxy, $proxy);

        if ($wallet['useProxy']) {
            $qiwi = new Qiwi(
                    $wallet['login'], $wallet['password'],
                    $wallet['proxy']['host'] . ":" . $wallet['proxy']['port'],
                    $wallet['proxy']['login'] . ":" . $wallet['proxy']['password']
            );
        } else {
            $qiwi = new Qiwi($wallet['login'], $wallet['password']);
        }
        $qiwi->login();

        return $qiwi;
    }

    /**
     * Get instance of wallet from DB if it exists or newly created temporary wallet object.
     *
     *
     * Good Lord, I wish php had function overloading........
     *
     * @param $login
     * @param null $password
     * @param null $useProxy
     * @param array $proxy
     * @return array
     */
    public static function getQiwiInstanceWallet($login, $password = null, $useProxy = null, $proxy = []) {
        if ($password == null) {
            $wallet = QiwiWallet::where("login", $login)->first();
            $login = $wallet->login;
            $password = $wallet->password;
            $useProxy = $wallet->use_proxy;
            $proxy = Proxy::find($wallet->proxy_id);
        }

        return [
                'login' => $login,
                'password' => $password,
                'useProxy' => $useProxy,
                'proxy' => $proxy,
        ];
    }


    public static function getTodaysExpenditure($login) {
        $control = self::getQiwiInstance($login);

        return $control->getTotals(date("d.m.Y"), date("d.m.Y"))['expenditure'];
    }

    public static function getBalance($login) {
        $control = self::getQiwiControlObject($login);

        return $control->loadBalance()['RUB'];
    }

    public static function getBalance2($login) {
        $control = self::getQiwiInstance($login);
        if (!isset(((array) $control->getBalance())['RUB'])) return null;

        return ((array) $control->getBalance())['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = self::getQiwiInstance($login);
        $income = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];

        return $income;
    }

    // detect provider by first numbers
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