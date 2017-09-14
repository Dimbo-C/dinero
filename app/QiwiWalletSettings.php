<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class QiwiWalletSettings extends Model {
    protected $table = "qiwi_wallet_settings";
    protected $primaryKey = "wallet_id";
    public $incrementing = false;

    public function findByLogin($login) {
        $wallet = (new QiwiWallet())->findByLogin($login);
        $settings = $this->find($wallet->id);

        return $settings;
    }

    public function updateWithData($data, $id) {
        $autoWithdrawTypeId = (new AutowithdrawTypes())->findBySlug($data->autoWithdrawalType)->id;

        $settings = $this->find($id);

        $settings->comments = $data->comments;
        $settings->is_always_online = $data->alwaysOnline;
        $settings->balance_recheck_timeout = $data->balanceRecheckTimeout;
        $settings->maximum_balance = $data->maximumBalance;
        $settings->using_vouchers = $data->usingVouchers;
        $settings->maximum_balance = $data->maximumBalance;

        $settings->autoWithdrawal_active = $data->autoWithdrawalActive;
        $settings->autoWithdrawal_type_id = $autoWithdrawTypeId;
        $settings->autoWithdrawal_minutes = $data->autoWithdrawalTimeout;
        $settings->autoWithdrawal_target = $data->withdrawTarget;

        $settings->autoWithdrawal_card_number = $data->autoWithdrawalCardNumber;
        $settings->autoWithdrawal_cardholder_name = $data->autoWithdrawalCardholderName;
        $settings->autoWithdrawal_cardholder_surname = $data->autoWithdrawalCardholderSurname;

        $settings->save();
    }

    public function bindToWallet($walletId) {
        $this->insert([
                'wallet_id' => $walletId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    public function isAutoWidthdrawalActive() {
        return $this->autoWithdrawal_active;
    }

    public function isTimeToWithdraw() {
        $now = Carbon::now();
        $end = Carbon::parse($this->last_withdraw_time);
        $len = $end->diffInMinutes($now);

        return $len >= $this->autoWithdrawal_minutes;
    }
}
