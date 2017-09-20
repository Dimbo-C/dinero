<?php

namespace App\Helpers;

use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use App\Repositories\QiwiWalletRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QiwiWalletUpdateHelper {

    public static function isTimeToUpdate($login) {
        $settings = (new QiwiWalletSettings)->findByLogin($login);

        if ($settings->balance_recheck_timeout == 0) return false;

        $now = Carbon::now();
        $end = Carbon::parse($settings->last_balance_recheck);

        $len = $end->diffInMinutes($now);

        return $len >= $settings->balance_recheck_timeout;
    }

    public static function updateWallet($login) {
        if (!self::isTimeToUpdate($login)) return false;

        $repository = new QiwiWalletRepository();

        return $repository->updateBalanceAndIncome($login);
    }

    public static function restoreSession($login) {
        $wallet = (new QiwiWallet())->findByLogin($login);
        $settings = QiwiWalletSettings::find($wallet->id);
        $proxy = Proxy::find($wallet->proxy_id);

        if ($settings->is_always_online) {
            $control = QiwiGeneralHelper::getQiwiControlObject($login, $wallet->password, $wallet->use_proxy, $proxy);
//            Log::info("Session for wallet " . $login . " refreshed");
            return $control->login();
        }

        return false;
    }
}