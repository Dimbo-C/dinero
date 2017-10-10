<?php

namespace App\Services;

use App\AutowithdrawTypes;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use Illuminate\Support\Facades\Log;

define("AUTOWITHDRAW_EVERY_X_MINUTES", 1);
define("AUTOWITHDRAW_AFTER_BALANCE_UPDATE", 2);
define("AUTOWITHDRAW_MANUALLY", 3);
define("AUTOWITHDRAW_TARGET_CARD", "card");
define("AUTOWITHDRAW_TARGET_WALLET", "wallet");

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

    private $login;


    function __construct($walletLogin) {
        $this->login = $walletLogin;
        $this->init();
    }

    // fetch some data
    private function init() {
        $this->wallet = QiwiWallet::where("login", $this->login)->first();
        $this->settings = QiwiWalletSettings::find($this->wallet->id);
        $this->autoWithdrawType = AutowithdrawTypes::find($this->settings->autoWithdrawal_type_id);
    }

    /**
     * Run the shit
     * @param $autowithdrawMode int one of constants
     * @return bool
     */
    public function autoWithdraw($autowithdrawMode) {
        Log::info("Before guards " . $this->login);
        if (!$this->guards($autowithdrawMode)) return false;
        Log::info("Guards passed " . $this->login);
        switch ($autowithdrawMode) {
            case AUTOWITHDRAW_EVERY_X_MINUTES:
                Log::info("In every x minutes " . $this->login);
                $this->everyXMin();
                break;

            default:
                return false;
        }

        return true;
    }

    private function everyXMin() {
        $target = $this->settings->autoWithdrawal_target;
        switch ($target) {
            case "wallet":
                return $this->toWallet();
            case "card":
                Log::info("Shit is rad: " . $this->login);
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
            $sum = 1;
            $currency = "RUB";
            $comment = "Автовывод с кошелька " . $this->login . " " . date("d.m.y");
            Log::info("Before send action");
            Withdraw::toCreditCard($login, $cardnum, $fname, $lname, $sum, $currency, $comment);
            Log::info("send action success");
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    private function toWallet() {
        return true;
    }

    // all necessary checks before proceeding to executing auto withdraw
    private function guards($mode) {
        Log::info("Before mode right");
        if (!$this->isModeRight($mode)) return false;
        Log::info("mode is right");
        if (!$this->settings->isAutoWidthdrawalActive()) return false;
        Log::info("autowithdraw active");

        return true;
    }

    private function isModeRight($mode) {
        switch ($mode) {
            case AUTOWITHDRAW_EVERY_X_MINUTES:
                Log::info("Inside mode checker: ");
                Log::info("Every x minutes : " . ($this->autoWithdrawType->isEveryXMinutes() ? "true" : "false"));
                Log::info("Is time to withdraw : " . ($this->settings->isTimeToWithdraw() ? "true" : "false"));
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
}