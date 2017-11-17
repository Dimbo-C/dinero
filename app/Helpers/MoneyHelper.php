<?php

namespace App\Helpers;

class MoneyHelper {

    public static function moneyToFloat($money, $rus = true) {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);
        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;
        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

        $result = (float) str_replace(',', '.', $removedThousandSeparator);
        $result = ($rus ? $result / 100 : $result);
        //        setlocale(LC_MONETARY, 'ru_RU');
        //        $result = money_format('%.2n', $result);
        $result = number_format($result, 2);

        return $result;
    }

    public static function getCurrency($str) {
        $str = str_replace(chr(194) . chr(160), "", $str);
        if (preg_match("/([а-яА-Яa-zA-Z\\.]+)$/u", $str, $m)) {
            return $m[1];
        }

        return false;
    }

    public static function getBaseCost($amount, $percentageToSubstract, $additional = 0) {
        $divider = 1 + $percentageToSubstract / 100;
        $amount -= $additional;
        $resultAmount = $amount / $divider;

        return $resultAmount;
    }

    public static function getCardWithdrawAmount($withdrawAmount,$cardNumber) {
        $type = QiwiGeneralHelper::detectCardProvider($cardNumber);
        switch ($type) {
            case "VISA_VIRTUAL":
                return self::getBaseCost($withdrawAmount, 1);

            // VISA standard and all others
            default:
                return self::getBaseCost($withdrawAmount, 1, 50);
        }
    }
}