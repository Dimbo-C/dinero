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
        $result = new WithdrawResult();

        $responseData = json_decode($qiwiControl->getResponseData())->data;
        $result->customData = $responseData;

        $result->status = trim($responseData->status);
        $result->resultText = $responseData->status == 200
                ? "<b>Отлично!</b> Вы успешно совершили перевод с Qiwi кошелька $login
                 на сумму $amount RUB"
                : "<b>Ошибка!</b> " . trim($responseData->body->message);
        $result->debugData = $responseData;

        //        return self::getNiceResult($qiwiControl);
        return $result;
    }

    public static function toCreditCard($login, $cardNumber, $firstName, $lastName, $sum, $currency, $comment = "") {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);

        return self::getNiceResult($qiwiControl);
    }

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
        dd($qiwiControl);
        $result->status = 200;
        if ($result->error != null) {
            $result->status = 400;
            $result->resultText = $result->error;
        } else {
            if (json_decode($qiwiControl->getResponseData()) == null) {
                $result->resultText = $qiwiControl->getResponseData();
            } else {
                $responseData = json_decode($qiwiControl->getResponseData())->data;
                $result->customData = $responseData;

                $result->status = trim($responseData->status);
                $result->resultText = $responseData->status == 200
                        ? "Операция успешна"
                        : trim($responseData->body->message);
                $result->debugData = $responseData;
            }
        }

        return $result;
    }
}