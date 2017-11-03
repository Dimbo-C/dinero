<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\QiwiWalletSettings
 *
 * @property int $wallet_id
 * @property string|null $comments
 * @property int $is_always_online
 * @property int $balance_recheck_timeout
 * @property string $last_balance_recheck
 * @property float $maximum_balance
 * @property int $autoWithdrawal_active
 * @property string $autoWithdrawal_target
 * @property int|null $autoWithdrawal_type_id
 * @property int $autoWithdrawal_minutes
 * @property float $autoWithdrawal_minimum_withdraw_amount
 * @property string $last_withdrawal_time
 * @property int $using_vouchers
 * @property string|null $autoWithdrawal_card_number
 * @property string|null $autoWithdrawal_cardholder_name
 * @property string|null $autoWithdrawal_cardholder_surname
 * @property string|null $autoWithdrawal_wallet_number
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalCardholderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalCardholderSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalMinimumWithdrawAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalWalletNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereBalanceRecheckTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereIsAlwaysOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereLastBalanceRecheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereLastWithdrawalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereMaximumBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereUsingVouchers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereWalletId($value)
 * @mixin \Eloquent
 * @property float $autoWithdrawal_limit
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalLimit($value)
 * @property string|null $autoWithdrawal_wallet_numbers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\QiwiAutowithdrawWallets[] $autoWithdrawalWallets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletSettings whereAutoWithdrawalWalletNumbers($value)
 */
class QiwiWalletSettings extends Model {
    protected $fillable = array('wallet_id');
    protected $table = "qiwi_wallet_settings";
    protected $primaryKey = "wallet_id";
    public $incrementing = false;

    private $walletSplitter = "; ";

    public function findByLogin($login) {
        $wallet = (new QiwiWallet())->findByLogin($login);
        $settings = $this->find($wallet->id);

        return $settings;
    }

    public function updateSettings($data, $login) {
        $autoWithdrawTypeId = (new AutowithdrawTypes())->findBySlug($data->autoWithdrawalType)->id;

        $settings = $this->findByLogin($login);

        $settings->comments = $data->comments;
        $settings->is_always_online = $data->alwaysOnline;
        $settings->balance_recheck_timeout = $data->balanceRecheckTimeout;
        $settings->maximum_balance = $data->maximumBalance;
        $settings->using_vouchers = $data->usingVouchers;
        $settings->maximum_balance = $data->maximumBalance;

        $settings->autoWithdrawal_active = $data->autoWithdrawalActive;
        $settings->autoWithdrawal_type_id = $autoWithdrawTypeId;
        $settings->autoWithdrawal_target = $data->autoWithdrawalTarget;
        $settings->autoWithdrawal_minutes = $data->autoWithdrawalTimeout;

        $settings->autoWithdrawal_card_number = $data->autoWithdrawalCardNumber;
        $settings->autoWithdrawal_cardholder_name = $data->autoWithdrawalCardholderName;
        $settings->autoWithdrawal_cardholder_surname = $data->autoWithdrawalCardholderSurname;

        $settings->autoWithdrawal_minimum_withdraw_amount = $data->autoWithdrawalMinBalance;

        $settings->autoWithdrawal_wallet_numbers = $this->implodeWalletNumbers($data->autoWithdrawalWallets);
        $settings->autoWithdrawal_limit = $data->autoWithdrawalLimit;

        $settings->save();

        //        $settings->persistAutoWithdrawWallets($data->autoWithdrawalWallets, $settings);


    }

    public function implodeWalletNumbers(array $walletNumbers) {
        return implode($this->walletSplitter, $walletNumbers);
    }

    public function explodeWalletNumbers($walletNumbersString) {
        return explode($this->walletSplitter, $walletNumbersString);
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
        $end = Carbon::parse($this->last_withdrawal_time);
        $diff = $end->diffInMinutes($now);
        $isTime = $diff >= $this->autoWithdrawal_minutes;

//        if(!$isTime) Log::info("")
        return $isTime;
    }

    public function updateWithdrawalTimer() {
        $this->last_withdrawal_time = Carbon::now();

        $this->save();
    }

    /**
     * @param $data
     * @param $model QiwiWalletSettings
     */
    public function persistAutoWithdrawWallets($wallets, $model) {
        //        $model->autoWithdrawalWallets()->detach();

        foreach ($wallets as $wallet) {
            $model->autoWithdrawalWallets()->create([
                    'number' => $wallet
            ]);
        }
    }

    public function autoWithdrawalWallets() {
        return $this->belongsToMany('App\QiwiAutowithdrawWallets',
                'qiwi_settings_autowithdraw_wallets',
                "master_wallet_id",
                "autowithdraw_wallet_id"
        );
    }
}
