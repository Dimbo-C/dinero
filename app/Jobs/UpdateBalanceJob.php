<?php

namespace App\Jobs;

use App\QiwiWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class UpdateBalanceJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /* @var QiwiWallet */
    private $wallet;

    /**
     * UpdateBalanceJob constructor.
     * @param $login
     */
    public function __construct($login) {
        $this->wallet = QiwiWallet::findByLogin($login);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ($this->wallet == null) {
            Log::error("Wallet not found in Update balance job");
            throw new Exception("Nigger please stay in field and grab dem shovel");
        }

        dump($this->wallet);
    }

    public function failed() {
        Log::error("I caught a nigger! Yay");
    }
}
