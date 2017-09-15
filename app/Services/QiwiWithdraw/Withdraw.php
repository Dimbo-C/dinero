<?php

namespace App\Services;

use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;

class Withdraw {

    public static function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $wallet = QiwiWallet::where("login", $login)->first();
        $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;

        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
                $wallet->login,
                $wallet->password,
                $wallet->use_proxy,
                $proxy);

        $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        return [
                "login" => $login,
                "error" => $qiwiControl->getLastError(),
                "qiwicontrol" => $qiwiControl
        ];
    }
}