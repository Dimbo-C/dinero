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
     * @param null $proxy_id
     * @return integer id if new wallet
     */
    public function insertWallet($data, $proxy_id = NULL) {
        $newWallet = new QiwiWallet();
        $newWallet->name = $data->name;
        $newWallet->login = $data->login;
        $newWallet->password = $data->password;
        $newWallet->is_active = $data->is_active;
        $newWallet->type_id = $data->typeId;
        $newWallet->balance = $data->balance;
        $newWallet->month_income = $data->monthIncome;
        $newWallet->proxy_id = $proxy_id;
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
        $settings['wallet_settings'] = QiwiWalletSettings::find($id);
        $settings['wallet_types'] = QiwiWalletType::all();
        $settings['autowithdraw_types'] = AutowithdrawTypes::all();

        return $settings;
    }


}
