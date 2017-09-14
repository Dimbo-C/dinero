<?php

use App\AutowithdrawTypes;
use App\QiwiWallet;
use App\QiwiWalletSettings;

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
     * @param $walletLogin string
     */
    function __construct($walletLogin) {
        $this->init($walletLogin);
    }

    private function init($login) {
        $this->wallet = QiwiWallet::where("login", $login)->first();
        $this->settings = QiwiWalletSettings::find($this->wallet->id);
        $this->autoWithdrawType = AutowithdrawTypes::find($this->settings->autoWithdrawal_type_id);
    }

    public function autoWithdraw(int $autowithdrawMode) {
        if (!$this->guards($autowithdrawMode)) return;
        $a = 3;


    }


    //    private function

    private function guards($mode) {
        if (!$this->isModeRight($mode)) return false;
        if (!$this->settings->isAutoWidthdrawalActive()) return false;

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
        }

        return false;
    }
}