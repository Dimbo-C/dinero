<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QiwiAutowithdrawWallets extends Model {
    protected $fillable = ['id','number'];

    public function settings() {
        return $this->belongsToMany('App\QiwiWalletSettings',
                'qiwi_settings_autowithdraw_wallets',
                "autowithdraw_wallet_id",
                "master_wallet_id");

        //        return $this->belongsToMany('App\QiwiWalletSettings');
    }
}
