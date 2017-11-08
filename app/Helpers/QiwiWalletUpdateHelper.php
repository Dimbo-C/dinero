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
        $wallet = QiwiWallet::findByLogin($login);
        if ($wallet == null) return false;

        $settings = $wallet->settings;

        if ($settings->balance_recheck_timeout == 0) return false;

        $now = Carbon::now();
        $end = Carbon::parse($settings->last_balance_recheck);

        $diffInSeconds = $end->diffInSeconds($now);

        // correction in calculations to prevent small inequalities in compared seconds
        $diffInSeconds += 20;

        return $diffInSeconds >= $settings->balance_recheck_timeout * 60;
    }

    public static function updateBalance($login) {
        if (!QiwiWalletUpdateHelper::isTimeToUpdate($login)) return false;

        $balance = QiwiGeneralHelper::getBalance($login);
        $wallet = QiwiWallet::findByLogin($login);
        if ($balance != null) $wallet->updateBalance($balance);

        $monthIncome = QiwiGeneralHelper::getMonthIncome($login);
        $wallet->updateIncome($monthIncome);
        if ($monthIncome != null) $wallet->updateIncome($monthIncome);

        return [
                "balance" => $balance,
                "monthIncome" => $monthIncome
        ];
    }

    public static function restoreSession($login) {
        $wallet = (new QiwiWallet())->findByLogin($login);
        $settings = QiwiWalletSettings::find($wallet->id);
        $proxy = Proxy::find($wallet->proxy_id);

        if ($settings->is_always_online) {
            $control = QiwiGeneralHelper::getQiwiControlObject(
                    $login, $wallet->password, $wallet->use_proxy, $proxy);

            return $control->login();
        } else {
            return false;
        }
    }
}