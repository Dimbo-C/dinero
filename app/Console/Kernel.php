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
            $wallets = QiwiWallet::all();
            foreach ($wallets as $wallet) {
                QiwiWalletUpdateHelper::updateWallet($wallet->login);
                Log::info("Wallet :" . $wallet->login);
            }
            Log::info("end of scheduled task");
        })->everyMinute();
        // $schedule->command('inspire')
        //          ->hourly();
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
