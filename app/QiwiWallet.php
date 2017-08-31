<?php

namespace App;

use Cazzzt\Qiwi\Qiwi1;
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
     * @param $type_id
     * @param $balance
     * @param $month_income
     * @param null $proxy_id
     * @return bool
     */
    public function insertWallet($data, $type_id, $balance, $month_income, $proxy_id = NULL) {
        $newWallet = new QiwiWallet();
        $newWallet->name = $data->name;
        $newWallet->login = $data->login;
        $newWallet->password = $data->password;
        $newWallet->is_active = $data->is_active;
        $newWallet->type_id = $type_id;
        $newWallet->balance = $balance;
        $newWallet->month_income = $month_income;
        $newWallet->proxy_id = $proxy_id;

        return $newWallet->save();
    }
}
