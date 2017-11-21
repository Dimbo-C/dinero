<?php

namespace App\Services;

use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use App\Helpers\QiwiGeneralHelper;
use App\Structures\WithdrawResult;
use Illuminate\Support\Facades\Log;

class Withdraw {
    /**
     * @param $login
     * @param $to
     * @param $currency
     * @param $amount
     * @param bool $comment
     * @return WithdrawResult
     */
    public static function toQiwiWallet($login, $to, $currency, $amount, $comment = false) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        if (!$qiwiControl->login()) {
            Log::error("Cant login into $login");
        } else {
            Log::info("Logged into $login");
        };
        $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        if ($result->error != null) $qiwiControl->transferMoney($to, $currency, $amount, $comment);

        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();

        Log::error("Error in Withdraw#toQiwiWallet:");
        Log::error($result->error);
        $result->debugData = $qiwiControl->debugData;
        $result->status = 200;
        if ($result->error != null && $result->error != "") {
            $result->status = 400;
            $result->resultText = "<b>Ошибка!</b> " . $result->error;
        } else {
            $result->resultText = "<b>Успех! </b>Перевод на сумму $amount совершен<br>";
        }

        return $result;
    }

    /**
     * @param $login
     * @param $cardNumber
     * @param $firstName
     * @param $lastName
     * @param $sum
     * @param $currency
     * @param string $comment
     * @return WithdrawResult|bool
     */
    public static function toCreditCard($login, $cardNumber, $firstName, $lastName, $sum, $currency, $comment = "") {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        Log::info("Sum right before transfer: $sum");
        $result = $qiwiControl->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);

        Log::info("Result: " . ($result ? "true" : "false"));
        Log::info("Error:");
        Log::info($qiwiControl->getLastError());
        Log::info("After error");


        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        $result->debugData = $qiwiControl->debugData;
        $result->status = 200;

        //        Log::info("Error before : ");
        //        Log::info($result->error);

        if ($result->error != null) {
            $result->status = 400;
            Log::info("Error in toCard : ");
            Log::info((array) $result->error);
            //            dd($result->error);
            $result->resultText = "<b>Ошибка!</b> " . $result->error;
        } else {
            $result->resultText = "<b>Отлично! </b>Вы успешно совершили перевод с QIWI кошелька "
                    . str_replace("+", "", $login) . " на сумму $sum RUB<br>";
        }

        return $result;
    }

    /**
     * @param $login
     * @param $sum
     * @return WithdrawResult
     */
    public static function purchaseVoucher($login, $sum) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->purchaseVoucher($sum);

        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        $result->debugData = $qiwiControl->debugData;
        $result->status = 200;
        $responseText = $qiwiControl->getResponseData();
        if ($result->error != null) {
            $result->status = 400;
            $result->resultText = "<b>Ошибка!</b> $responseText";;
        } else {
            $result->resultText = "<b>Ваучер: </b>$responseText<br>
                <b>Сумма: </b>$sum RUB<br><b>Статус: </b>Создан";
        }

        return $result;
    }

    public static function activateVoucher($login, $code) {
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject($login);
        $qiwiControl->activateVoucher($code);

        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        $result->debugData = $qiwiControl->debugData;
        $result->status = 200;
        if ($result->error != null) {
            $result->status = 400;
            $result->resultText = $result->error;
        } else {
            $result->resultText = $qiwiControl->getResponseData();
        }

        return $result;
    }

    /**
     * Create an object that has 'status' and 'resultText' fields
     * that will be used on front-end
     *
     * @param $qiwiControl QIWIControl
     * @return WithdrawResult
     */
    private static function getNiceResult($qiwiControl) {
        $result = new WithdrawResult();
        $result->error = $qiwiControl->getLastError();
        $result->debugData = $qiwiControl->debugData;

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