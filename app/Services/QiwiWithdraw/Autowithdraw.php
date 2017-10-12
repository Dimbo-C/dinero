<?php

namespace App\Services;

use App\AutowithdrawTypes;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use Illuminate\Support\Facades\Log;

define("AUTOWITHDRAW_EVERY_X_MINUTES", 1);
define("AUTOWITHDRAW_AFTER_BALANCE_UPDATE", 2);
define("AUTOWITHDRAW_MANUALLY", 3);

class Autowithdraw {
    /**
     * @var QiwiWallet
     */
    private $wallet;
    /**
     * @var QiwiWalletSettings
     */
    private $settings;
    /**
     * @var AutowithdrawTypes
     */
    private $autoWithdrawType;

    /**
     * @var string
     */
    private $login;

    /**
     * @var double
     */

    private $withdrawAmount;

    function __construct($walletLogin) {
        $this->login = $walletLogin;
        $this->init();
    }

    // fetch some data
    private function init() {
        $this->wallet = QiwiWallet::where("login", $this->login)->first();
        $this->settings = QiwiWalletSettings::find($this->wallet->id);
        $this->autoWithdrawType = AutowithdrawTypes::find($this->settings->autoWithdrawal_type_id);
        $this->withdrawAmount = $this->withdrawAmount();
    }

    private function withdrawAmount() {
        $balance = $this->wallet->balance;
        $limit = $this->settings->autoWithdrawal_limit;
        if ($limit == 0) return $balance;

        $amount = $balance > $limit ? $limit : $balance;

        //        // TODO: TEST STATE remove in production
        //        $amount = 1;

        return $amount;
    }

    /**
     * Run the shit
     * @param $autowithdrawMode int one of constants
     * @return bool
     */
    public function autoWithdraw($autowithdrawMode) {
        Log::info("Before guards: " . $this->login);
        if (!$this->guards($autowithdrawMode)) return false;
        Log::info("Guards passed: " . $this->login);
        $result = $this->withdrawRoutine();

        // update timer if action was successful
        if ($result) $this->settings->updateWithdrawalTimer();
        Log::info("Autowithdraw from " . $this->login . " is " . ($result ? "successful" : "failed"));

        return $result;
    }

    /**
     * @return bool success
     */
    private function withdrawRoutine() {
        $target = $this->settings->autoWithdrawal_target;
        switch ($target) {
            case "wallet":
                return $this->toWallet();
            case "card":
                return $this->toCard();
            default:
                return false;
        }
    }

    private function toCard() {
        try {
            $login = $this->login;
            $cardnum = $this->settings->autoWithdrawal_card_number;
            $fname = $this->settings->autoWithdrawal_cardholder_name;
            $lname = $this->settings->autoWithdrawal_cardholder_surname;
            $sum = $this->withdrawAmount;
            $currency = "RUB";
            $comment = "Автовывод с кошелька " . $this->login . " " . date("d.m.y H:i:s");
            $result = Withdraw::toCreditCard(
                    $login, $cardnum, $fname,
                    $lname, $sum, $currency, $comment);

            return ($result->error == null);
        } catch (\Exception $ex) {
            Log::error("Error in 'AutoWithdraw#toCard()'");

            return false;
        }
    }

    private function toWallet() {
        try {
            $to = $this->settings->autoWithdrawal_wallet_number;
            $amount = $this->withdrawAmount;
            $comment = "Автовывод с кошелька " . $this->login . " " . date("d.m.y H:i:s");
            $result = Withdraw::toQiwiWallet($this->login, $to, "RUB", $amount, $comment);

            return ($result->error == null);
        } catch (\Exception $ex) {
            Log::error("Error in 'AutoWithdraw#toWallet()'");
            return false;
        }
    }

    // all necessary checks before proceeding to executing auto withdraw
    private function guards($mode) {
        if (!$this->isModeRight($mode)) return false;
        if (!$this->settings->isAutoWidthdrawalActive()) return false;
        if (!$this->isEnoughMoney()) return false;

        return true;
    }

    private function isModeRight($mode) {
        switch ($mode) {
            case AUTOWITHDRAW_EVERY_X_MINUTES:
                if ($this->autoWithdrawType->isEveryXMinutes() && $this->settings->isTimeToWithdraw()) {
                    return true;
                }
                break;

            case AUTOWITHDRAW_AFTER_BALANCE_UPDATE:
                if ($this->autoWithdrawType->isAfterBalanceUpdate()) return true;
                break;

            case AUTOWITHDRAW_MANUALLY:
                if ($this->autoWithdrawType->isManually()) return true;
                break;
            default:
                return false;
        }

        return false;
    }

    private function isEnoughMoney() {
        if ($this->wallet->balance < $this->settings->autoWithdrawal_minimum_withdraw_amount) {
            Log::info("Not enough money for auto withdraw");
            return false;
        }

        return true;
    }
}