<?php

namespace App\Console;

use App\Helpers\QiwiWalletUpdateHelper;
use App\QiwiWallet;
use App\Services\Autowithdraw;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->call(function () {
            $this->autoWithdraw();
            $this->updateBalances();
            $this->refreshSessions();
        })->everyMinute();
    }

    private function updateBalances() {
        foreach (QiwiWallet::all() as $wallet) {
            QiwiWalletUpdateHelper::updateBalance($wallet->login);
        }
    }

    private function refreshSessions() {
        foreach (QiwiWallet::all() as $wallet) {
            QiwiWalletUpdateHelper::restoreSession($wallet->login);
        }
    }

    private function autoWithdraw() {
        foreach (QiwiWallet::all() as $wallet) {
            $aw = new Autowithdraw($wallet->login);
            $aw->autoWithdraw(AUTOWITHDRAW_EVERY_X_MINUTES);
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands() {
        require base_path('routes/console.php');
    }
}
