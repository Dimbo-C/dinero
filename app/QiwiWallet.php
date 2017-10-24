<?php

namespace App;

use App\Services\Autowithdraw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 * App\QiwiWallet
 *
 * @property int $id
 * @property int $type_id
 * @property int $use_proxy
 * @property int|null $proxy_id
 * @property string $name
 * @property string $login
 * @property string $password
 * @property string|null $balance
 * @property string|null $month_income
 * @property int $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\QiwiWalletType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereMonthIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereUseProxy($value)
 * @mixin \Eloquent
 * @property-read \App\QiwiWalletSecuritySettings $securitySettings
 * @property-read \App\QiwiWalletSettings $settings
 */
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|QiwiWalletSettings
     */
    public function settings() {
        return $this->hasOne('App\QiwiWalletSettings', 'wallet_id');
    }

    public function securitySettings() {
        return $this->hasOne('App\QiwiWalletSecuritySettings', 'wallet_id');
    }

    public function deleteByIds($ids) {
        if (count($ids) == 0) return 0;

        $this->cleanProxies($ids);

        return $this->destroy($ids);
    }

    // remove proxies of specific wallets
    private function cleanProxies($walletIds) {
        $wallets = $this->find($walletIds);
        $proxyIds = [];
        foreach ($wallets as $wallet) {
            $proxyIds[] = $wallet['proxy_id'];
        }
        (new Proxy())->deleteByIds($proxyIds);
    }

    /**
     * @param $data
     * @param null $proxyId
     * @return QiwiWallet new wallet
     */
    public static function insertWallet($data, $proxyId = null) {
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

        return $newWallet;
    }

    // not sure if it should be used ever again but, meh
    public function updateBalanceAndIncome($balance, $monthIncome) {
        $this->balance = $balance;
        $this->month_income = $monthIncome;
        $this->save();

        $this->postUpdateRoutine();
    }

    public function updateBalance($balance) {
        $this->balance = $balance;
        $this->save();

        $this->postUpdateRoutine();

        return $this;
    }

    public function updateIncome($income) {
        $this->month_income = $income;
        $this->save();

        $this->postUpdateRoutine();

        return $this;
    }

    /**
     * @param $login
     * @return QiwiWallet|Model
     */
    public static function findByLogin($login) {
        return (new QiwiWallet())->where("login", $login)->first();
    }

    public static function findByName($name) {
        return (new QiwiWallet())->where("name", $name)->first();
    }

    public static function walletExists($login) {
        return (new QiwiWallet())->where('login', $login)->exists();
    }

    public static function walletExistsByName($name) {
        return (new QiwiWallet())->where('name', $name)->exists();
    }

    private function postUpdateRoutine() {
        $this->updateRecheckTime();
        $this->recheckMaximumBalance();

        // auto withdraw attempt
        $aw = new Autowithdraw($this->login);
        $aw->autoWithdraw(AUTOWITHDRAW_AFTER_BALANCE_UPDATE);
    }

    // put wallet to reserve if it's maximum balance is more that current balance
    private function recheckMaximumBalance() {
        if ($this->balance >= $this->settings->maximum_balance) {
            $walletTypeId = (new QiwiWalletType())->findByType("reserve")->id;

            $this->type_id = $walletTypeId;

            $this->save();
        }
    }

    private function updateRecheckTime() {
        //        $settings = (new QiwiWalletSettings())->findByLogin($login);
        $this->settings->last_balance_recheck = Carbon::now()->format('Y-m-d H:i:s');
        $this->settings->save();
    }
}
