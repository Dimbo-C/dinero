<?php

namespace App\Jobs;

use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateBalanceJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /* @var string */
    private $login;

    /**
     * UpdateBalanceJob constructor.
     * @param $login
     */
    public function __construct($login) {
        $this->login = $login;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //        if ($this->wallet == null) {
        //            Log::error("Wallet not found in Update balance job");
        //            throw new Exception("Nigger please stay in field and grab dem shovel");
        //        }

        $monthIncome = QiwiGeneralHelper::getMonthIncome($this->login);

        $wallet = QiwiWallet::findByLogin($this->login);
        $wallet->updateIncome($monthIncome);
        dump($wallet);
    }

    public function failed() {
        Log::error("I caught a nigger! Yay");
    }
}
