<?php

namespace App\Helpers;


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
    public static function getQiwiControlObject($login, $password, $useProxy, $proxy) {
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


    /**
     *  Get qiwi service object
     * @param $login
     * @return Qiwi
     */
    public static function getQiwiInstance($login) {
        $wallet = QiwiWallet::where("login", $login)->first();

        if ($wallet->use_proxy) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password, $proxy->host . ":" . $proxy->port);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }

        return $qiwi;
    }
}