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
    public function updateBalanceAndIncome($login, $balance, $monthIncome) {
        $wallet = $this->findByLogin($login);
        $wallet->balance = $balance;
        $wallet->month_income = $monthIncome;
        $wallet->save();

        $this->postUpdateRoutine($login);
    }

    public function updateBalance($login, $balance) {
        $wallet = $this->findByLogin($login);
        $wallet->balance = $balance;
        $wallet->save();

        $this->postUpdateRoutine($login);
    }

    public function updateIncome($login, $income) {
        $wallet = $this->findByLogin($login);
        $wallet->month_income = $income;
        $wallet->save();

        $this->postUpdateRoutine($login);
    }

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


    public function updateByLogin($login, $arguments = []) {
        $this->where("login", $login)->update($arguments);
        $this->postUpdateRoutine($login);
    }

    private function postUpdateRoutine($login) {
        $this->updateRecheckTime($login);
        $this->recheckMaximumBalance($login);

        // auto withdraw attempt
        $aw = new Autowithdraw($login);
        $aw->autoWithdraw(AUTOWITHDRAW_AFTER_BALANCE_UPDATE);
    }

    // put wallet to reserve if it's maximum balance is more that current balance
    private function recheckMaximumBalance($login) {
        $wallet = $this->findByLogin($login);
        $settings = (new QiwiWalletSettings())->findByLogin($login);
        if ($wallet->balance >= $settings->maximum_balance) {
            $walletTypeId = (new QiwiWalletType())->findByType("reserve")->id;

            $wallet->type_id = $walletTypeId;
            $wallet->save();
        }
    }

    private function updateRecheckTime($login) {
        $settings = (new QiwiWalletSettings())->findByLogin($login);
        $settings->last_balance_recheck = Carbon::now()->format('Y-m-d H:i:s');
        $settings->save();
    }

}
