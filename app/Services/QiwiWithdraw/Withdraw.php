<?php

namespace App\Services;

use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;

class Withdraw {

    public static function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $qiwiControl = self::getControlObject($login);
        $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        return [
                "login" => $login,
                "error" => $qiwiControl->getLastError(),
                "qiwicontrol" => $qiwiControl
        ];
    }

    public static function toCreditCard($login, $cardNumber, $firstName, $lastName, $sum, $currency, $comment = "") {
        $qiwiControl = self::getControlObject($login);
        $qiwiControl->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);
    }

    public static function viaVoucher($login) {
        $qiwiControl = self::getControlObject($login);
        $result = $qiwiControl->sendVoucherViaEmail("dimon220495@gmail.com", "RUB", 3, "Voucher comment");

        return [
                'error' => $qiwiControl->getLastError(),
                'result' => $result
        ];
    }

    private static function getControlObject($login) {
        $wallet = QiwiWallet::where("login", $login)->first();
        $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;

        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
                $wallet->login,
                $wallet->password,
                $wallet->use_proxy,
                $proxy);

        $qiwiControl->login();

        return $qiwiControl;
    }
}