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
        $control = self::getQiwiControlObject($login);

        return $control->loadBalance()['RUB'];
    }

    public static function getMonthIncome($login) {
        $qiwi = self::getQiwiInstance($login);
        $income = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];

        return $income;
    }

    public static function emailBinding($login, $mail) {
        $qiwiControl = self::getQiwiControlObject($login);
        $response = $qiwiControl->bindEmail($mail);
        //        dump($response);

        $success = false;
        if ($response) {
            $success = (intval(json_decode($response)->code->value) == 0);
        }


        $token[] = $qiwiControl->getLastError();
        $token[] = $qiwiControl->getResponseData();

        //        $success = $qiwiControl->setQIWIWalletOwnerData(
        //                "Semenov",
        //                "Maksim",
        //                "Viktorovych",
        //                "1992-02-02",
        //                "4400111222",
        //                "",
        //                "440100732259",
        //                ""
        //        );

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    public static function emailFetchToken($login) {
        $qiwiControl = self::getQiwiControlObject($login);
        $response = $qiwiControl->emailUnbindingToken();
        //        dd($response);
        $response = json_decode($response);

        $success = $response->code->_name == "CONFIRM";
        $token = $success ? $response->identifier : "null";

        $args[] = $qiwiControl->getLastError();
        $args[] = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token,
                'args' => $args
        ];
    }

    public static function emailUnbinding($login, $code, $token) {
        $qiwiControl = self::getQiwiControlObject($login);
        $response = $qiwiControl->unbindEmail($code, $token);
        if ($response) {
            $success = (intval(json_decode($response)->code->value) == 0);
        } else {
            $success = false;
        }

        $args[] = $qiwiControl->getLastError();
        $args[] = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token,
                'args' => $args
        ];
    }

    public static function setSecurityAttribute($login, $attribute, $enabled) {
        $qiwiControl = self::getQiwiControlObject($login);
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
        $qiwiControl = self::getQiwiControlObject($login);
        $success = $qiwiControl->setQIWISecuritySetting("SMS_CONFIRMATION", $enabled);
        $token = $qiwiControl->getResponseData();

        return [
                'success' => $success,
                'token' => $token
        ];
    }

    public static function userConfirmBySMS($login, $token, $code) {
        $qiwiControl = self::getQiwiControlObject($login);
        $success = $qiwiControl->userConfirmBySMS("SMS_CONFIRMATION", $token, $code);

        return ['success' => $success];
    }

    public static function getSecuritySettings($login) {
        $qiwiControl = self::getQiwiControlObject($login, null, false, []);
        $settings = $qiwiControl->getQIWISecuritySettings();
        $qiwiControl->removeCookies();

        return $settings;
    }

    public static function getIdentification($login) {
        $qiwiControl = self::getQiwiControlObject($login);
        $identification = $qiwiControl->getQIWIWalletOwnerData();

        return $identification;
    }

    public static function updateIdentification($data) {
        $qiwiControl = self::getQiwiControlObject($data->login);
//        $qiwiControl->login();

        $settings = $qiwiControl->setQIWIWalletOwnerData(
                $data->lastName,
                $data->firstName,
                $data->middleName,
                $data->birthDate,
                $data->passport,
                $data->snils,
                $data->inn,
                $data->oms
        );

        return $settings;
    }
}