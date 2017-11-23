<?php

namespace Tests\Feature;

use App\Helpers\MoneyHelper;
use Tests\TestCase;

class BaseCostTest extends TestCase {
    /**
     * when min balance is bigger
     *
     * @return void
     */
    public function testMinBalanceBigger() {
        $amount = 100;
        $percentage = 1;
        $minimumBalance = 50;
        $baseCost = MoneyHelper::getBaseCost($amount, $percentage, $minimumBalance);

        $this->assertTrue($baseCost == 50);
    }


    /**
     * when min balance is smaller
     *
     * @return void
     */
    public function testMinBalanceSmaller() {
        $amount = 100;
        $percentage = 50;
        $minimumBalance = 1;
        $baseCost = MoneyHelper::getBaseCost($amount, $percentage, $minimumBalance);

        $this->assertTrue($baseCost == 100 * 2 / 3);
    }

    public function testCardSubstractVisaVirtual() {
        $amount = 100;
        $cardnum = "4890494408571107";
        $resultAmount = MoneyHelper::cardSubtractStaticTax($amount, $cardnum);

        $this->assertTrue($resultAmount == $amount);
    }

    public function testCardSubstractAnyCard() {
        $amount = 100;
        $cardnum = "52100494408571107";
        $resultAmount = MoneyHelper::cardSubtractStaticTax($amount, $cardnum);

        $this->assertTrue($resultAmount < $amount);
    }
}
