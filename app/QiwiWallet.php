<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class QiwiWallet extends Model {
    protected $table = "qiwi_wallets";
    protected $fillable = ['login', 'password', 'name', 'balance', 'month_income', 'is_active', 'type_id'];

    /**
     * Получаем тип, к которому относится кошелек.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type() {
        return $this->belongsTo(QiwiWalletType::class);
    }

    /**
     * @param $data
     * @param null $proxyId
     * @return integer id if new wallet
     */
    public function insertWallet($data, $proxyId = null) {
        $newWallet = new QiwiWallet();
        $newWallet->name = $data->name;
        $newWallet->login = $data->login;
        $newWallet->password = $data->password;
        $newWallet->is_active = $data->isActive;
        $newWallet->type_id = $data->typeId;
        $newWallet->balance = $data->balance;
        $newWallet->month_income = $data->monthIncome;
        $newWallet->use_proxy = $proxyId != null;
        $newWallet->proxy_id = $proxyId;
        $newWallet->save();

        return $newWallet->id;
    }

    public function getSettings($login) {
        $tmpWallet = $this->findByLogin($login);

        $settings['wallet'] = $tmpWallet;
        $settings['walletSettings'] = QiwiWalletSettings::find($tmpWallet->id);
        $settings['walletTypes'] = QiwiWalletType::all();
        $settings['autoWithdrawTypes'] = AutowithdrawTypes::all();
        $settings['id'] = $tmpWallet->id;
        $settings['proxy'] = Proxy::find($tmpWallet->proxy_id);

        return $settings;
    }

    public function updateBalanceAndIncome($login, $balance, $monthIncome) {
        $wallet = $this->findByLogin($login);

        $wallet->balance = $balance;
        $wallet->month_income = $monthIncome;

        $wallet->save();

        $this->postUpdateRoutine($login);

    }

    public function updateBalance($login, $balance) {
        $wallet = $this->findByLogin($login);
        $wallet->balance = $balance;
        $wallet->save();

        $this->postUpdateRoutine($login);
    }

    public function findByLogin($login) {
        return $this->where("login", $login)->first();
    }

    public function updateByLogin($login, $arguments = []) {
        $this->where("login", $login)->update($arguments);
        $this->postUpdateRoutine($login);
    }

    private function postUpdateRoutine($login) {
        $this->updateRecheckTime($login);
        $this->recheckMaximumBalance($login);
    }

    // put wallet to reserve if it's maximum balance is more that current balance
    private function recheckMaximumBalance($login) {
        $wallet = $this->findByLogin($login);
        $settings = (new QiwiWalletSettings())->findByLogin($login);
        if ($wallet->balance >= $settings->maximum_balance) {
            $walletTypeId = (new QiwiWalletType())->findByType("reserve")->id;

            $wallet->type_id = $walletTypeId;
            $wallet->save();
        }
    }

    private function updateRecheckTime($login) {
        $settings = (new QiwiWalletSettings())->findByLogin($login);
        $settings->last_balance_recheck = Carbon::now()->format('Y-m-d H:i:s');
        $settings->save();
    }
}
