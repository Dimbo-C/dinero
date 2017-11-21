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
        $balance = QiwiGeneralHelper::getBalance($this->login);
        $wallet = QiwiWallet::findByLogin($this->login);
        if ($balance != null) $wallet->updateBalance($balance);
        Log::info("Update balance job");
        dump($wallet);
    }

    public function failed() {
        Log::error("Error in UpdateBalanceJob");
    }
}
