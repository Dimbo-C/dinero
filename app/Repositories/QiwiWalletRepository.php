<?php

namespace App\Repositories;

use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletType;
use App\Services\Qiwi\Qiwi;

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
    public function getDataFromQiwi($data) {

        $control = new \QIWIControl($data->login, $data->password);
        $control->login();
        $balance = $control->loadBalance()['RUB'];
        $monthIncome = $control->loadBills(QIWI_BILLS_MODE_IN, date("01.m.Y"), date("d.m.Y"));
        $result['balance'] = $balance;
        $result['month_income'] = count($monthIncome) > 0 ? $monthIncome : $balance;
        QiwiWallet::where("login", $data->login)
                ->update([
                        'balance' => $result['balance'],
                        'month_income' => $result['month_income']
                ]);

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

    public function reportFor($query, $id) {
        $wallet = $this->findByLogin($id);

        if ($wallet->proxy_id != null) {
            $proxy = Proxy::find($wallet->proxy_id);
            $qiwi = new Qiwi($wallet->login, $wallet->password, $proxy->host . ":" . $proxy->port);
        } else {
            $qiwi = new Qiwi($wallet->login, $wallet->password);
        }

        // test
        //        $qiwi = new Qiwi($wallet->login, $wallet->password, "216.56.48.118:9000");

        //        $walletNumber = "+79250308492";
        //        $walletNumber = $wallet->login;


        //        return Cache::remember("$walletNumber.{$query['start']}-{$query['end']}", 5, function () use ($qiwi, $query) {
        $report = $qiwi->reportForDateRange($query['start'], $query['end']);

        return $report;
        //        });
    }

    /**
     * Add new wallet
     * @param $data
     * @return mixed
     */
    public function insertWallet($data) {
        // test
        //        $data->login = "+380960968460";
        //        $data->password = "Kekroach2204";
        //        $data->name = "test";

        // get new proxy
        $proxy = $this->createProxy($data);

        if (is_object($proxy)) {
            $proxyData = $data['proxy'];
            $controlProxy = $proxyData['host'] . ":" . $proxyData['port'];
            $controlProxyAuth = $proxyData['login'] . ":" . $proxyData['password'];
            $control = new \QIWIControl($data->login, $data->password, "cookie_data", $controlProxy, $controlProxyAuth);
        } else {
            $control = new \QIWIControl($data->login, $data->password);
        }

        if (!$control->login()) {
            $result['status'] = "failure";
            $result['message'] = "Кошелек не найден в системе Qiwi";

            return $result;
        };

        $typeId = (new QiwiWalletType)->where("slug", $data->type)->first()->id;
        $balance = $control->loadBalance()['RUB'];

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        $monthIncome = $balance;
//                $monthIncome = $control->loadBills(QIWI_BILLS_MODE_IN, date("01.m.Y"), date("d.m.Y"));

        // add new wallet to DB with proxy or not
        $wallet = new QiwiWallet();
        if (is_object($proxy)) {
            $wallet->insertWallet($data, $typeId, $balance, $monthIncome, $proxy->id);
        } else {
            $wallet->insertWallet($data, $typeId, $balance, $monthIncome);
        }

        $result['status'] = "success";
        $result['message'] = "Кошелек успешно добавлен.";

        return $result;
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





