<?php

namespace App\Services;

use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;
use App\Structures\WithdrawResult;

class Withdraw {
    public static function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $qiwiControl = self::getControlObject($login);
        $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        return self::getNiceResult($qiwiControl);
    }

    public static function toCreditCard($login, $cardNumber, $firstName, $lastName, $sum, $currency, $comment = "") {
        $qiwiControl = self::getControlObject($login);
        $qiwiControl->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);

        return self::getNiceResult($qiwiControl);
    }

    public static function viaVoucher($login, $targetField, $currency, $sum, $comment) {
        $qiwiControl = self::getControlObject($login);
        //        $email = "dimon220495@gmail.com";

        $qiwiControl->sendVoucherViaEmail($targetField, $currency, $sum, $comment);

        return self::getNiceResult($qiwiControl);
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

    /**
     * @param $qiwiControl QIWIControl
     * @return WithdrawResult
     */
    private static function getNiceResult($qiwiControl) {
        $result = new WithdrawResult();

        $result->error = $qiwiControl->getLastError();

        if ($result->error != null) {
            $result->status = 400;
            $result->resultText = $result->error;
        } else {
            $responseData = json_decode($qiwiControl->getResponseData())->data;
            $result->customData = $responseData;

            $result->status = trim($responseData->status);
            $result->resultText = $responseData->status == 200
                    ? "Операция успешна"
                    : trim($responseData->body->message);
        }

        return $result;
    }
}