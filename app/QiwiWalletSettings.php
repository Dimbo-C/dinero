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
}
