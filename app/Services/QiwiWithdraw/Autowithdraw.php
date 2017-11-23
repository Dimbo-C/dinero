<?php

namespace App\Services;

use App\AutowithdrawTypes;
use App\Helpers\MoneyHelper;
use App\Helpers\QiwiGeneralHelper;
use App\Jobs\UpdateBalanceJob;
use App\Jobs\UpdateIncomeJob;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use App\QiwiWalletType;
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
    private $force;
    private $autoWithdrawMode;

    /**
     * Autowithdraw constructor.
     *
     * @param $walletLogin
     * @param bool $force
     */
    function __construct($walletLogin, $force = false) {
        $this->login = $walletLogin;
        $this->force = $force;
        $this->init();
    }

    // fetch some data
    private function init() {
        $this->wallet = QiwiWallet::findByLogin($this->login);
        $this->settings = $this->wallet->settings;
        $this->autoWithdrawType = AutowithdrawTypes::find($this->settings->autoWithdrawal_type_id);
        //        $this->withdrawAmount = $this->calculateWithdrawAmount();
    }

    /**
     * @param null $autowithdrawMode
     * @return bool
     */
    public function autoWithdraw($autowithdrawMode = null) {
        $this->autoWithdrawMode = $autowithdrawMode;
        Log::info("Autowithdraw start for {$this->login} mode: $autowithdrawMode");
        $guardsPassed = $this->guards();
        Log::info("Guards passed for {$this->login}: " . ($guardsPassed ? "TRUE" : "FALSE"));

        if (!$guardsPassed) return false;

        $this->withdrawAmount = $this->calculateWithdrawAmount();

        Log::info("Trying to withdraw {$this->withdrawAmount} from {$this->login}");
        Log::info("Autowithdraw routine for " . $this->login);
        $success = $this->withdrawRoutine();

        // update timer if action was successful
        if ($success) {
            $this->postWithdrawRoutine();
            $this->settings->updateWithdrawalTimer();
        }

        Log::info("Autowithdraw from {$this->login} " . ($success ? "successful" : "failed"));

        return $success;
    }

    /**
     * @return bool success
     */
    private function withdrawRoutine() {
        $target = $this->settings->autoWithdrawal_target;
        Log::info("Target in routine: $target");
        switch ($target) {
            case "wallet":
                $walletsNumbers = $this->settings->explodeWalletNumbers($this->settings->autoWithdrawal_wallet_numbers);
                $result = $this->toWallet($walletsNumbers);
                break;

            case "withdrawals":
                $walletsNumbers = QiwiWalletType::allAutoWithdrawals(true);
                $result = $this->toWallet($walletsNumbers);
                break;

            case "withdrawals_wallet":
                $walletsNumbers = QiwiWalletType::autoWithdrawalsNumber(true);
                $result = $this->toWallet($walletsNumbers);
                break;

            case "card":
                $result = $this->toCard();
                break;

            default:
                $result = false;
        }

        return $result;
    }

    private function postWithdrawRoutine() {
        Log::info("Post withdraw routine");
        $updateBalanceJob = new UpdateBalanceJob($this->wallet->login);
        dispatch($updateBalanceJob);

        $updateIncomeJob = new UpdateIncomeJob($this->wallet->login);
        dispatch($updateIncomeJob);
    }

    // all necessary checks before proceeding to executing auto withdraw
    private function guards() {
        if (!$this->force) {
            if (!$this->isModeRight()) return false;
            if (!$this->wallet->settings->isAutoWidthdrawalActive()) return false;
        }
        if (!$this->isEnoughMoney()) return false;

        return true;
    }

    private function isModeRight() {
        switch ($this->autoWithdrawMode) {
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
        $login = $this->login;
        $cardnum = $this->settings->autoWithdrawal_card_number;

        $fname = $this->settings->autoWithdrawal_cardholder_name;
        if (trim($fname) == "") $fname = "A";
        $lname = $this->settings->autoWithdrawal_cardholder_surname;
        if (trim($lname) == "") $lname = "A";

        $sum = $this->withdrawAmount;
        $currency = "RUB";
        //            $comment = "Автовывод с кошелька " . $this->login . " " . date("d.m.y H:i:s");
        $comment = "Автовывод {$this->login} -> $cardnum";
        Log::info("Comment: $comment");
        $result = Withdraw::toCreditCard($login, $cardnum, $fname, $lname, $sum, $currency, $comment);

        return ($result->error == null);
    }

    private function toWallet($walletsNumbers) {
        Log::info("in toWallet");
        $result = true;
        try {
            foreach ($walletsNumbers as $walletNumber) {
                // stop this madness
                if ($this->withdrawAmount <= 1) return false;

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

                $comment = "Автовывод {$this->login} -> $to";
                Log::info($comment);

                $result = Withdraw::toQiwiWallet($this->login, $to, "RUB", $this->withdrawAmount, $comment);
                Log::error("Error: " . $result->error);
                Log::error("Error len: " . strlen($result->error));

                // update wallet if it is over maximum balance
                if ("" == trim($result->error) || null == $result->error) {
                    Log::info("Success routine");
                    $receivingWallet = QiwiWallet::findByLogin($to);
                    $receivingWallet->balance += $this->withdrawAmount;
                    $receivingWallet->updateIfOverTheMax();
                    Log::info("Right before dispatch of balance update");
                    dispatch(new UpdateBalanceJob($to));

                    break;
                } else {
                    $result = false;
                }
            }

            return $result;
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

        $minimumBalance = floatval($this->settings->minimum_balance);

        switch ($this->settings->autoWithdrawal_target) {
            case "withdrawals":
            case "withdrawals_wallet":
            case "wallet":
                $withdrawAmount = MoneyHelper::getBaseCost($withdrawAmount, 1, $minimumBalance);
                break;

            case "card":
                $withdrawAmount = MoneyHelper::cardSubtractStaticTax(
                        $withdrawAmount,
                        $this->wallet->settings->autoWithdrawal_card_number
                );
                $withdrawAmount = MoneyHelper::getBaseCost($withdrawAmount, 2, $minimumBalance);
                //                $withdrawAmount = MoneyHelper::getCardWithdrawAmount(
                //                        $withdrawAmount,
                //                        $this->wallet->settings->autoWithdrawal_card_number,
                //                        $minimumBalance + 50
                //                );
                break;
        }
        Log::info("Calculated withdraw amount: $withdrawAmount");

        return $withdrawAmount;
    }
}