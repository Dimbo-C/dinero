<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\QiwiWalletSecuritySettings
 *
 * @mixin \Eloquent
 * @property int $wallet_id
 * @property int $sms_confirmation
 * @property int $email_binding
 * @property string $email
 * @property int $use_token
 * @property int $use_pin_code
 * @property int $sms_payments
 * @property int $call_confirm
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereCallConfirm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereEmailBinding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereSmsConfirmation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereSmsPayments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereUsePinCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereUseToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSecuritySettings whereWalletId($value)
 */
class QiwiWalletSecuritySettings extends Model {
    protected $fillable = ["wallet_id"];
    protected $table = "qiwi_wallet_security_settings";
    protected $primaryKey = "wallet_id";
    public $incrementing = false;

}
