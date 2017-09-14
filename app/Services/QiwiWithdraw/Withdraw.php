<?php

use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;

class Withdraw {

    public function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $wallet = QiwiWallet::where("login", $login)->first();
        if ($wallet->use_proxy) {
            $proxy = Proxy::find($wallet->proxy_id);
        }
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
                $wallet->login,
                $wallet->password,
                $wallet->use_proxy,
                $proxy);

        $qiwiControl->transferMoney($to, $currency, $amount, $comment);
    }
}