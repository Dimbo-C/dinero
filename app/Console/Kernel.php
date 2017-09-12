<?php

namespace App\Console;

use App\Helpers\QiwiWalletUpdateHelper;
use App\QiwiWallet;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
            $this->updateBalances();
            $this->refreshSessions();
        })->everyMinute();
    }

    private function updateBalances() {
        foreach (QiwiWallet::all() as $wallet) {
            QiwiWalletUpdateHelper::updateWallet($wallet->login);
        }
    }

    private function refreshSessions() {
        foreach (QiwiWallet::all() as $wallet) {
            QiwiWalletUpdateHelper::restoreSession($wallet->login);
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
