<?php

namespace App\Services;

use App\AutowithdrawTypes;
use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use App\Repositories\QiwiWalletRepository;
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
    private $login;
    private $withdrawAmount;

    function __construct($walletLogin) {
        $this->login = $walletLogin;
        $this->init();
    }

    // fetch some data
    private function init() {
        $this->wallet = QiwiWallet::findByLogin($this->login);
        $this->settings = QiwiWalletSettings::find($this->wallet->id);
        $this->autoWithdrawType = AutowithdrawTypes::find($this->settings->autoWithdrawal_type_id);
        //        $this->withdrawAmount = $this->calculateWithdrawAmount();
    }

    /**
     * @param $autowithdrawMode
     * @param bool $force
     * @return bool
     */
    public function autoWithdraw($autowithdrawMode, $force = false) {
        Log::info("Autowithdraw start for " . $this->login);
        $guardsPassed = $this->guards($autowithdrawMode, $force);
        Log::info("Guards passed for {$this->login}: " . ($guardsPassed ? "TRUE" : "FALSE"));

        if (!$guardsPassed) return false;

        $this->withdrawAmount = $this->calculateWithdrawAmount();

        Log::info("Autowithdraw routine for " . $this->login);
        $result = $this->withdrawRoutine();

        // update timer if action was successful
        if ($result) {
            $this->settings->updateWithdrawalTimer();
        }

        Log::info("Autowithdraw from {$this->login} " . ($result ? "successful" : "failed"));

        return $result;
    }

    /**
     * @return bool success
     */
    private function withdrawRoutine() {
        $target = $this->settings->autoWithdrawal_target;
        switch ($target) {
            case "wallet":
                $result = $this->toWallet();
                break;

            case "card":
                $result = $this->toCard();
                break;

            default:
                $result = false;
        }
        $this->postWithdrawRoutine();

        return $result;
    }

    private function postWithdrawRoutine() {
        $control = QiwiGeneralHelper::getQiwiControlObject($this->wallet->login);

        $newBalance = $control->loadBalance()['RUB'];
        $this->wallet->updateBalance($newBalance);

        $monthIncome = QiwiGeneralHelper::getMonthIncome($this->wallet->login);
        $this->wallet->updateIncome($monthIncome);
    }

    // all necessary checks before proceeding to executing auto withdraw
    private function guards($mode, $force = false) {
        if (!$force) {
            if (!$this->isModeRight($mode)) return false;
            if (!$this->wallet->settings->isAutoWidthdrawalActive()) return false;
        }

        if (!$this->isEnoughMoney()) return false;

        return true;
    }

    private function isModeRight($mode) {
        switch ($mode) {
            case AUTOWITHDRAW_EVERY_X_MINUTES:
                if ($this->autoWithdrawType->isEveryXMinutes() && $this->settings->isTimeToWithdraw()) return true;
                break;

            case AUTOWITHDRAW_AFTER_BALANCE_UPDATE:
                if ($this->autoWithdrawType->isAfterBalanceUpdate()) return true;
                break;

            case AUTOWITHDRAW_MANUALLY:
                if ($this->autoWithdrawType->isManually()) return true;
                break;
        }

        return false;
    }

    private function isEnoughMoney() {

        Log::info("Login: " . $this->wallet->login);
        Log::info("Balance: " . $this->wallet->balance);
        Log::info("Min amount: " . $this->wallet->settings->autoWithdrawal_minimum_withdraw_amount);

        if ($this->wallet->balance < $this->settings->autoWithdrawal_minimum_withdraw_amount) {
            Log::info("Login: {$this->wallet->login} NOT ENOUGH MONEYS");
            return false;
        }

        return true;
    }

    private function toCard() {
        //        try {
        $login = $this->login;
        $cardnum = $this->settings->autoWithdrawal_card_number;
        $fname = $this->settings->autoWithdrawal_cardholder_name;
        $lname = $this->settings->autoWithdrawal_cardholder_surname;
        $sum = $this->withdrawAmount;
        $currency = "RUB";
        //            $comment = "Автовывод с кошелька " . $this->login . " " . date("d.m.y H:i:s");
        $comment = "Автовывод {$this->login} -> $cardnum";
        $result = Withdraw::toCreditCard($login, $cardnum, $fname, $lname, $sum, $currency, $comment);

        return ($result->error == null);
        //        } catch (\Exception $ex) {
        //            Log::error("Error in 'AutoWithdraw#toCard()'");
        //
        //            return false;
        //        }
    }

    private function toWallet() {
        try {
            if ($this->settings->autoWithdrawal_wallet_numbers == "") return false;

            $walletsNumbers = $this->settings->explodeWalletNumbers($this->settings->autoWithdrawal_wallet_numbers);
            //            $amount = $this->withdrawAmount;

            foreach ($walletsNumbers as $walletNumber) {
                // stop this madness
                if ($this->withdrawAmount <= 1) return true;

                // prepend + if it is absent
                if (strpos($walletNumber, "+") === false) $walletNumber = trim("+$walletNumber");

                // get target wallet
                $autoWithdrawWallet = QiwiWallet::findByLogin($walletNumber);
                if ($autoWithdrawWallet == null) continue;

                // do not withdraw to this wallet if it has balance exceeding the maximum
                if ($autoWithdrawWallet->balance >= $autoWithdrawWallet->settings->maximum_balance) {
                    continue;
                }

                $to = $walletNumber;

                Log::info("Withdrawing {$this->login} -> $to. Amount: {$this->withdrawAmount}");
                $comment = "Автовывод {$this->login} -> $to";
                $result = Withdraw::toQiwiWallet($this->login, $to, "RUB", $this->withdrawAmount, $comment);
                Log::error("Error: " . $result->error);
            }

            return true;
        } catch (\Exception $ex) {
            Log::error("Error in 'AutoWithdraw#toWallet()'");

            return false;
        }
    }

    // calculate exactly how much moneys should be withdrawed now
    private function calculateWithdrawAmount() {
        $balance = $this->wallet->balance;
        $limit = $this->settings->autoWithdrawal_limit;

        $withdrawAmount = $balance > $limit ? $limit : $balance;

        switch ($this->settings->autoWithdrawal_target) {
            case "wallet":
//                $expenditure = QiwiGeneralHelper::getTodaysExpenditure($this->wallet->login);
//                if ($expenditure > 100000) $withdrawAmount *= 0.99;
                $withdrawAmount *= 0.99;
                break;

            case "card":
                $withdrawAmount = $this->getCardWithdrawAmount($withdrawAmount);
                break;

            default:
                break;
        }
        Log::info("Calculated withdraw amount: $withdrawAmount");

        return $withdrawAmount;
    }


    private function getCardWithdrawAmount($withdrawAmount) {
        $type = QiwiGeneralHelper::detectCardProvider($this->wallet->settings->autoWithdrawal_card_number);
        switch ($type) {
            case "VISA_VIRTUAL":
                return $withdrawAmount * 0.99;

            // VISA standard and all others
            default:
                return ($withdrawAmount - 50) * 0.98;
        }
    }
}