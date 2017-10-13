<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QiwiWalletSecuritySettings extends Model {
    protected $fillable = ["wallet_id"];
    protected $table = "qiwi_wallet_security_settings";
    protected $primaryKey = "wallet_id";
    public $incrementing = false;

}
