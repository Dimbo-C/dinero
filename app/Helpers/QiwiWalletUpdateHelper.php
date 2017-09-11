<?php

namespace App\Helpers;

use App\QiwiWalletSettings;
use App\Repositories\QiwiWalletRepository;
use Carbon\Carbon;

class QiwiWalletUpdateHelper {

    public static function isTimeToUpdate($login) {
        $settings = (new QiwiWalletSettings)->findByLogin($login);

        // stop if option is turned off
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

    public static function kek($login) {

    }

}