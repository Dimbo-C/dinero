<?php

namespace App\Jobs;

use App\Helpers\QiwiGeneralHelper;
use App\QiwiWallet;
use function GuzzleHttp\Promise\exception_for;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateIncomeJob implements ShouldQueue {
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
        Log::info("Update income job started");
        try {

            $qiwi = QiwiGeneralHelper::getQiwiInstance($this->login);
            $qiwi->setNewTimeout(40);
            $monthIncome = $qiwi->getTotals(date("01.m.Y"), date("d.m.Y"))['income'];

            //            $monthIncome = QiwiGeneralHelper::getMonthIncome($this->login);

            $wallet = QiwiWallet::findByLogin($this->login);
            $wallet->updateIncome($monthIncome);
            Log::info("Update income job income is $monthIncome");
            Log::info("Update income job finished");
            dump($wallet);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            //            $this->failed();
            //            echo "<pre>";
            //            print_r($exception);
            //            echo "</pre>";
        }
    }

    public function failed() {
        //        $this->
        Log::error("Error in UpdateIncomeJob");
    }
}
