<?php

namespace App\Repositories;

use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Helpers\QiwiGeneralHelper;
use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use App\QiwiWalletType;
use App\Services\Qiwi\Qiwi;
use Illuminate\Support\Facades\Log;
use QIWIControl;

class QiwiWalletRepository implements Contract {
    private $staticWallet;

    function __construct() {
        $this->staticWallet = new QiwiWallet();
    }

    public function all() {
        return QiwiWalletType::with('wallets')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function moveWalletsTo($ids, $to) {
        QiwiWallet::whereIn('id', $ids)->update(['type_id' => $to, 'is_active' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        $wallet = QiwiWallet::where('id', $id)->first();

        return $wallet ?: null;
    }

    public function withdrawMoney($cardNumber, $firstName, $lastName, $sum, $currency, $comment) {
        // test lokodoco
        $cardNumber = "5168 7551 0409 9169";
        $lastName = "Cherkashyn";
        //        $lastName="Черкашин";
        $firstName = "Dmitriy";
        //        $firstName="Дмитрий";
        $control = new QIWIControl("+380960968460", "Kekroach2204");
        $transferResult = $control->transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $currency, $comment);
        Log::info("Transfer result: ");
        Log::info($transferResult);
        Log::info("Error: " . $control->getLastError());
    }

    /**
     * {@inheritdoc}
     */
    public function updateBalanceAndIncome($login) {
        $wallet = $this->findByLogin($login);
        $proxy = Proxy::find($wallet->proxy_id);

        // library instance
        $control = QiwiGeneralHelper::getQiwiControlObject($login, $wallet->password, $wallet->useProxy, $proxy);
        $control->login();

        // In-project class instance (cuz library's not working)
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);

        // get report for date range
        $tp = new TransactionProcessor($qiwi->reportForDateRange(date("01.m.Y"), date("d.m.Y")));

        // get RUB from result (doubt that there can be any other)
        $balance = $control->loadBalance()['RUB'];
        $monthIncome = $tp->getIncome();

        // update in DB
        $this->staticWallet->updateByLogin($login, [
                'balance' => $balance,
                "month_income" => $monthIncome
        ]);

        // get nice array with required data
        $updateResult = $this->formUpdateResult($monthIncome, $balance, [$monthIncome]);

        return $updateResult;
    }

    /**
     * @param $login
     * @return QiwiWallet
     */
    public function findByLogin($login) {
        $wallet = QiwiWallet::where('login', $login)->first();

        return $wallet ?: null;
    }

    /**
     * Get history of transactions for wallet
     * @param $query array of additional data
     * @param $login
     * @return array
     */
    public function reportFor($query, $login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);

        //        return Cache::remember("$walletNumber.{$query['start']}-{$query['end']}", 5, function () use ($qiwi, $query) {
        $transactionProcessor = new TransactionProcessor($qiwi->reportForDateRange($query['start'], $query['end']));
        $report = [
                'history' => $transactionProcessor->getTransactions(),
                'income' => $transactionProcessor->getIncome(),
                'outcome' => $transactionProcessor->getOutcome()
        ];

        return $report;
        //        });
    }

    /**
     * Get settings of specific wallet
     * @param $login string wallet
     * @return mixed
     */
    public function settings($login) {
        return $this->staticWallet->getSettings($login);
    }

    public function createWallet($data) {
        $request = $data;

        // create new proxy
        $proxyRepository = new ProxyRepository();
        $proxyRepository->create($data['proxy']);
        $proxy = $proxyRepository->getLast();

        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
                $request->login, $request->password, $request->useProxy, $request['proxy']);

        if (!$qiwiControl->login()) {
            $result['status'] = "failure";
            $result['message'] = "Кошелек не найден в системе Qiwi";

            return $result;
        };

        $request->typeId = (new QiwiWalletType())->findByType($request->type)->id;
        $request->balance = $qiwiControl->loadBalance()['RUB'];

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        $request->monthIncome = $request->balance;

        // add new wallet to DB with proxy or not
        $walletId = $this->staticWallet->insertWallet(
                $data,
                is_object($proxy) ? $proxy->id : null
        );

        // create a settings row in DB
        (new QiwiWalletSettings())->bindToWallet($walletId);

        $result['status'] = "success";
        $result['message'] = "Кошелек успешно добавлен.";

        return $result;
    }


    public function updateSettings($data) {
        // update wallet table
        $wallet = $this->updateWallet($data->login, $data->walletActive, $data->walletType, $data->useProxy);

        // update proxy for wallet
        if ($wallet->use_proxy) {
            Proxy::find($wallet->proxy_id)->update($data->proxy);
        }

        // update settings in settings table
        $this->updateWalletSettings($data, $data->login);
    }


    private function updateWalletSettings($data, $login) {
        $wallet = $this->staticWallet->findByLogin($login);
        (new QiwiWalletSettings)->updateWithData($data, $wallet->id);
    }

    private function updateWallet($login, $isActive, $walletType, $useProxy) {
        $typeId = (new QiwiWalletType())->findByType($walletType)->id;

        $wallet = $this->findByLogin($login);

        $wallet->is_active = $isActive;
        $wallet->type_id = $typeId;
        $wallet->use_proxy = $useProxy;
        $wallet->save();

        return $wallet;
    }

    private function formUpdateResult($monthIncome, $balance, $options = []) {
        return [
                "monthIncome" => $monthIncome,
                "balance" => $balance,
                "options" => $options,
        ];
    }

}