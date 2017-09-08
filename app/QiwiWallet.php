<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function insertWallet($data, $proxyId = NULL) {
        $newWallet = new QiwiWallet();
        $newWallet->name = $data->name;
        $newWallet->login = $data->login;
        $newWallet->password = $data->password;
        $newWallet->is_active = $data->isActive;
        $newWallet->type_id = $data->typeId;
        $newWallet->balance = $data->balance;
        $newWallet->month_income = $data->monthIncome;
        $newWallet->proxy_id = $proxyId;
        $newWallet->save();

        return $newWallet->id;
    }

    public function insertSettings($id) {
        DB::table('qiwi_wallet_settings')->insert([
                'wallet_id' => $id,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    public function settings($id) {
        $settings['wallet'] = $this->find($id);
        $settings['walletSettings'] = QiwiWalletSettings::find($id);
        $settings['walletTypes'] = QiwiWalletType::all();
        $settings['autoWithdrawTypes'] = AutowithdrawTypes::all();
        $settings['id'] = $id;

        return $settings;
    }

    public function updateBalanceAndIncome($login, $balance, $monthIncome) {
        $this->where("login", $login)->update([
                'balance' => $balance,
                'month_income' => $monthIncome
        ]);
    }


}
