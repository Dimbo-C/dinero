<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\QiwiAutowithdrawWallets
 *
 * @property int $id
 * @property string $number
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\QiwiWalletSettings[] $settings
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiAutowithdrawWallets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiAutowithdrawWallets whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiAutowithdrawWallets whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiAutowithdrawWallets whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
