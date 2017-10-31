<?php

namespace App\Services;

use App\AutowithdrawTypes;
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
        $this->withdrawAmount = $this->withdrawAmount();
    }

    /**
     * @param $autowithdrawMode
     * @param bool $force
     * @return bool
     */
    public function autoWithdraw($autowithdrawMode, $force = false) {
        Log::info("Autowithdraw start for " . $this->login);
        if (!$force && !$this->guards($autowithdrawMode)) return false;

        Log::info("Autowithdraw routine for " . $this->login);
        $result = $this->withdrawRoutine();

        // update timer if action was successful
        if ($result) {
            Log::info("Autowithdraw for {$this->login} was a success");
            $this->settings->updateWithdrawalTimer();
            $repo = new QiwiWalletRepository();
            $repo->updateIncome($this->login, false);
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
                return $this->toWallet();
            case "card":
                return $this->toCard();
            default:
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
                return true;
        }

        return false;
    }

    private function isEnoughMoney() {
        //        if ($this->wallet->balance < ($this->withdrawAmount() + $this->withdrawAmount() * 0.02)) {
        if ($this->wallet->balance < ($this->withdrawAmount())) {
            Log::info("Not enough money for auto withdraw");
            return false;
        }
        if ($this->wallet->balance < $this->settings->autoWithdrawal_minimum_withdraw_amount) {
            Log::info("Not enough money for auto withdraw");
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
        $result = Withdraw::toCreditCard(
                $login, $cardnum, $fname,
                $lname, $sum, $currency, $comment);

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
            $amount = $this->withdrawAmount;

            Log::info("Amount: $amount");
            Log::info($walletsNumbers);
            foreach ($walletsNumbers as $walletNumber) {
                // stop this madness
                if ($amount == 0) return true;

                // prepend + if it is absent
                if (strpos($walletNumber, "+") === false) $walletNumber = trim("+$walletNumber");

                // get target wallet
                $autoWithdrawWallet = QiwiWallet::findByLogin($walletNumber);
                if ($autoWithdrawWallet == null) continue;

                Log::info("Withdraw to $walletNumber");
                // if withdraw amount is more that it is possible to transfer to target wallet
                $maximumTransactionAmount = $autoWithdrawWallet->settings->maximum_balance - $autoWithdrawWallet->balance;
                if ($maximumTransactionAmount < $amount) {
                    $withdrawAmount = $maximumTransactionAmount;
                    $amount -= $withdrawAmount;
                } else {
                    $withdrawAmount = $amount;
                }

                $to = $walletNumber;

                $comment = "Автовывод {$this->login} -> $to";
                $result = Withdraw::toQiwiWallet($this->login, $to, "RUB", $withdrawAmount, $comment);
                Log::error("Error: " . $result->error);
            }

            return true;
        } catch (\Exception $ex) {
            Log::error("Error in 'AutoWithdraw#toWallet()'");

            return false;
        }
    }

    private function withdrawAmount() {
        $balance = $this->wallet->balance;
        $limit = $this->settings->autoWithdrawal_limit;
        if ($limit == 0) return $balance;

        $amount = $balance > $limit ? $limit : $balance;
        $amount *= 0.97;

        return $amount;
    }


}