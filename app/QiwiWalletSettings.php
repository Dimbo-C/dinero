<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QiwiWalletSettings extends Model {
    protected $table = "qiwi_wallet_settings";
    protected $primaryKey = "wallet_id";
    public $incrementing = false;

    public function findByLogin($login) {
        $wallet = QiwiWallet::where("login", "=", $login)->first();
        $settings = $this->find($wallet->id);

        return $settings;
    }

    public function updateWalletSettings($data, $id) {
        $settings = $this->find($id);

        $settings->comments = $data->comments;
        $settings->is_always_online = $data->alwaysOnline;
        $settings->balance_recheck_timeout = $data->balanceRecheckTimeout;
        $settings->maximum_balance = $data->maximumBalance;
        $settings->using_vouchers = $data->usingVouchers;
        $settings->maximum_balance = $data->maximumBalance;

        $settings->autoWithdrawal_card_number = $data->autoWithdrawalCardNumber;
        $settings->autoWithdrawal_cardholder_name = $data->autoWithdrawalCardholderName;
        $settings->autoWithdrawal_cardholder_surname = $data->autoWithdrawalCardholderSurname;

        $settings->save();
    }
}
