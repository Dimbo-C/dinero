<?php

namespace App\Repositories;

use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletSettings;
use App\QiwiWalletType;
use App\Services\Qiwi\Qiwi;
use QIWIControl;


class QiwiWalletRepository implements Contract {
    public function all() {
        return QiwiWalletType::with('wallets')->get();
    }

    /**
     * @param $ids
     * @param $to
     * @return mixed
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

    /**
     * {@inheritdoc}
     */
    public function updateBalanceAndIncome($data) {

        // library instance
        $control = new QIWIControl($data->login, $data->password);
        $control->login();

        // In-project class instance (cuz library's not working)
        $qiwi = $this->getQiwiInstance($data->login);

        // get report for date range
        $tp = new TransactionProcessor($qiwi->reportForDateRange(date("01.m.Y"), date("d.m.Y")));

        // get RUB from result (doubt that there can be any other)
        $balance = $control->loadBalance()['RUB'];
        $monthIncome = $tp->getIncome();

        // update in DB
        (new QiwiWallet())->updateBalanceAndIncome($data->login, $balance, $monthIncome);

        // get data array
        $result = $this->formUpdateResult($monthIncome, $balance, [$monthIncome]);

        return $result;
    }

    private function formUpdateResult($monthIncome, $balance, $optional = []) {
        $result['optional'] = $optional;
        $result['balance'] = $balance;
        $result['month_income'] = count($monthIncome) > 0 ? $monthIncome : $balance;

        return $result;
    }

    /**
     * @param $login
     * @return QiwiWallet
     */
    public function findByLogin($login) {
        $wallet = QiwiWallet::where('login', $login)->first();

        return $wallet ?: null;
    }

    public function getQiwiInstance($login) {
        $wallet = $this->findByLogin($login);

        if ($wallet->proxy_id != null) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password, $proxy->host . ":" . $proxy->port);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }

        return $qiwi;
    }

    public function reportFor($query, $id) {
        $qiwi = $this->getQiwiInstance($id);

        //        return Cache::remember("$walletNumber.{$query['start']}-{$query['end']}", 5, function () use ($qiwi, $query) {
        $transactionProcessor = new TransactionProcessor($qiwi->reportForDateRange($query['start'], $query['end']));

        $report['history'] = $transactionProcessor->getTransactions();
        $report['income'] = $transactionProcessor->getIncome();
        $report['outcome'] = $transactionProcessor->getOutcome();

        return $report;
        //        });
    }

    public function settings($login) {
        $id = QiwiWallet::where("login", "=", $login)->first()->id;

        return (new QiwiWallet)->settings($id);
    }

    /**
     * Add new wallet
     * @param $data
     * @return mixed
     */
    public function insertWallet($data) {
        $request = $data;

        // get new proxy
        $proxy = $this->createProxy($request);
        $control = $this->makeControl($request, $proxy);

        if (!$control->login()) {
            $result['status'] = "failure";
            $result['message'] = "Кошелек не найден в системе Qiwi";

            return $result;
        };

        $request->typeId = QiwiWalletType::where("slug", $request->type)->first()->id;
        $request->balance = $control->loadBalance()['RUB'];

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        $request->monthIncome = $request->balance;
        // $monthIncome = $control->loadBills(QIWI_BILLS_MODE_IN, date("01.m.Y"), date("d.m.Y"));

        // add new wallet to DB with proxy or not
        $wallet = new QiwiWallet();

        $newWalletId = $wallet->insertWallet(
                $data,
                is_object($proxy) ? $proxy->id : null
        );

        // create a settings row in DB
        $wallet->insertSettings($newWalletId);

        $result['status'] = "success";
        $result['message'] = "Кошелек успешно добавлен.";

        return $result;
    }

    // create qiwi-controlling object
    private function makeControl($data, $proxy) {
        if (is_object($proxy)) {
            $proxyData = $data['proxy'];
            $controlProxy = $proxyData['host'] . ":" . $proxyData['port'];
            $controlProxyAuth = $proxyData['login'] . ":" . $proxyData['password'];
            $control = new \QIWIControl($data->login, $data->password, "cookie_data", $controlProxy, $controlProxyAuth);
        } else {
            $control = new \QIWIControl($data->login, $data->password);
        }

        return $control;
    }


    public function saveSettings($data) {

        $this->updateWalletData($data);

        //        return $settings;
    }

    private function updateWalletData($data) {
        $this->updateWallet($data);
        $id = (new QiwiWallet)->where("login", $data->login)->first()->id;

        $this->updateWalletSettings($data, $id);

    }

    private function updateWalletSettings($data, $id) {

        (new QiwiWalletSettings)->updateWithData($data, $id);
    }

    private function updateWallet($data) {

        $typeId = QiwiWalletType::where("slug", $data->walletType)->first()->id;

        $wallet = QiwiWallet::where("login", $data->login)->first();
        $wallet->is_active = $data->walletActive;
        $wallet->type_id = $typeId;
        $wallet->save();

        return $wallet->id;
    }


    /**
     * insert data to DB
     * @param $data
     * @return bool|mixed
     *          false or proxy object
     */
    private function createProxy($data) {
        if ($data['use_proxy'] == false) return false;

        (new ProxyRepository())->create($data['proxy']);

        return Proxy::all()->last();
    }
}