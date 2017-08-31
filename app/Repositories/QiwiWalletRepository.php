<?php

namespace App\Repositories;

use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletType;
use App\Services\Qiwi\Qiwi;
use Illuminate\Support\Facades\Cache;

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
    public function findInQiwi($data) {

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
        $qiwi = new Qiwi($wallet->login, $wallet->password);
        $walletNumber = "+79250308492";

        return Cache::remember("$walletNumber.{$query['start']}-{$query['end']}", 5, function () use ($qiwi, $query) {
            $report = $qiwi->reportForDateRange($query['start'], $query['end']);

            return $report;
        });
    }

    /**
     * process form data and insert to DB
     * @param $data
     * @return mixed
     */
    public function storeWallet($data) {
        // test
        $data->login = "+380960968460";
        $data->password = "Kekroach2204";
        $data->name = "test";

        // get new proxy
        $proxy = $this->createProxy($data);


        $controlProxy = false;
        $controlProxyAuth = false;
        //        if (is_object($proxy)) {
        //            $controlProxy = $data['proxy']['host'] . ":" . $data['proxy']['port'];
        //            $controlProxyAuth = $data['proxy']['login'] . ":" . $data['proxy']['password'];
        //        }

        // qiwi custom api object
        $control = new \QIWIControl($data->login, $data->password, "cookie_data", $controlProxy, $controlProxyAuth);
        $result['control'] = $control;

        // check if login successful
        if (!$control->login()) {
            $result['status'] = "failure";
            $result['message'] = "Кошелек не найден в системе Qiwi";
            $result['proxy'] = $proxy;

            return $result;
        };

        $result['login'] = $control->login();

        // determine wallet type
        $typeId = (new QiwiWalletType)->where("slug", $data->type)->first()->id;

        // get balance from QIWI
        $balance = $control->loadBalance()['RUB'];

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        $monthIncome = $balance;
        //        $monthIncome = $control->loadBills(QIWI_BILLS_MODE_IN, date("01.m.Y"), date("d.m.Y"));

        $result['history'] = $monthIncome;

        // add new wallet to DB with proxy or not
        if (is_object($proxy)) {
            (new QiwiWallet())->insertWallet($data, $typeId, $balance, $monthIncome, $proxy->id);
        } else {
            (new QiwiWallet())->insertWallet($data, $typeId, $balance, $monthIncome);
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

        $proxyFields = ['host', 'port', 'login', 'password'];

        // return if data is blank. Fill the array for proxy along the way
        $proxyData = [];
        foreach ($proxyFields as $fieldName) {
            if ($data['proxy'][$fieldName] == "") return false;
            $proxyData[$fieldName] = $data['proxy'][$fieldName];
        }

        // insert new proxy to db
        (new ProxyRepository())->create($proxyData);
        $proxy = Proxy::all()->last();

        return $proxy;
    }
}





