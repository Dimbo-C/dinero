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
     * @param string $login
     * @param string $password
     * @param bool $useProxy
     * @param array $proxy
     * @return QIWIControl
     */
    public static function getQiwiControlObject($login, $password = null, $useProxy = false, $proxy = []) {
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

    public static function getBalance($login) {
        $control = QiwiGeneralHelper::getQiwiControlObject($login);

        return $control->loadBalance()['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $income = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];

        return $income;
    }

    public static function setSecurityAttribute($login, $attribute, $enabled) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->setQIWISecuritySetting($attribute, $enabled);
        $token = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    /**
     * @param $login
     * @param $enabled bool change setting to this state
     * @return array result
     *
     */
    public static function smsConfirmation($login, $enabled) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->setQIWISecuritySetting("SMS_CONFIRMATION", $enabled);
        $token = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    public static function userConfirmBySMS($login, $token, $code) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $success = $qiwiControl->userConfirmBySMS("SMS_CONFIRMATION", $token, $code);

        return ['success' => $success];
    }

    public static function getSecuritySettings($login) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login, null, false, []);
        $settings = $qiwiControl->getQIWISecuritySettings();
        $qiwiControl->removeCookies();

        return $settings;
    }
}