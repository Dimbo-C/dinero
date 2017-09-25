<?php

namespace App\Services;

use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;
use App\Structures\WithdrawResult;

class Withdraw {
    public static function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        return self::getNiceResult($qiwiControl);
    }

    public static function toCreditCard($login, $cardNumber, $firstName, $lastName, $sum, $currency, $comment = "") {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);

        return self::getNiceResult($qiwiControl);
    }

    //    public static function viaVoucher($login, $targetField, $currency, $sum, $comment) {
    //        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
    //        $qiwiControl->sendVoucherViaEmail($targetField, $currency, $sum, $comment);
    //
    //        return self::getNiceResult($qiwiControl);
    //    }

    public static function purchaseVoucher($login, $sum) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->purchaseVoucher($sum);

        return self::getNiceResult($qiwiControl);
    }

    public static function activateVoucher($login, $code) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->activateVoucher($code);

        return self::getNiceResult($qiwiControl);
    }

    //    private static function getControlObject($login) {
    //        $wallet = QiwiWallet::where("login", $login)->first();
    //        $proxy = $wallet->use_proxy ? Proxy::find($wallet->proxy_id) : null;
    //
    //        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
    //                $wallet->login,
    //                $wallet->password,
    //                $wallet->use_proxy,
    //                $proxy);
    //
    //        $qiwiControl->login();
    //
    //        return $qiwiControl;
    //    }

    /**
     * Create an object that has 'status' and 'resultText' fields
     * that will be used on front-end
     * @param $qiwiControl QIWIControl
     * @return WithdrawResult
     */
    private static function getNiceResult($qiwiControl) {
        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        $result->debugData = $qiwiControl->debugData;

        if ($result->error != null) {
            $result->status = 400;
            $result->resultText = $result->error;
        } else {
            $result->status = 200;
            if (json_decode($qiwiControl->getResponseData()) == null) {
                $result->resultText = $qiwiControl->getResponseData();
            } else {
                $responseData = json_decode($qiwiControl->getResponseData())->data;
                $result->customData = $responseData;

                $result->status = trim($responseData->status);
                $result->resultText = $responseData->status == 200
                        ? "Операция успешна"
                        : trim($responseData->body->message);
            }
        }


        return $result;
    }

    //    private static function
}