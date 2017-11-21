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

    public static function getBaseCost($amount, $percentageToSubtract, $minimumBalance = 0) {
//        $amount -= $minimumBalance;
        $divider = 1 + $percentageToSubtract / 100;
        $resultAmount = $amount / $divider;

        $taxAmount = $amount - $resultAmount;

        $baseCost = ($taxAmount < $minimumBalance)
                ? ($amount - $minimumBalance)
                : ($resultAmount);

        return $baseCost;
    }

//    public static function getCardWithdrawAmount($withdrawAmount, $cardNumber, $additionalSubtraction = 50) {
//        $type = QiwiGeneralHelper::detectCardProvider($cardNumber);
//        switch ($type) {
//            case "VISA_VIRTUAL":
//                return self::getBaseCost($withdrawAmount, 1);
//
//            // VISA standard and all others
//            default:
//                return self::getBaseCost($withdrawAmount, 1, $additionalSubtraction);
//        }
//    }

    // subtract static tax (50 rub) if card is not a visa virtual
    public static function cardSubtractStaticTax($withdrawAmount, $cardNumber) {
        $type = QiwiGeneralHelper::detectCardProvider($cardNumber);
        switch ($type) {
            case "VISA_VIRTUAL":
                return $withdrawAmount;

            // VISA standard and all others
            default:
                return $withdrawAmount - 50;
        }
    }
}