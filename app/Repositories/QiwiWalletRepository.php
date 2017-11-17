<?php

namespace App\Repositories;

use App\AutowithdrawTypes;
use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Helpers\QiwiGeneralHelper;
use App\Helpers\QiwiIdentificationHelper;
use App\Helpers\QiwiSecurityHelper;
use App\Jobs\UpdateBalanceJob;
use App\Jobs\UpdateIncomeJob;
use App\Processors\MassActionProcessor;
use App\Processors\TransactionProcessor;
use App\Proxy;
use App\QiwiWallet;
use App\QiwiWalletSecuritySettings;
use App\QiwiWalletSettings;
use App\QiwiWalletType;
use App\Services\Autowithdraw;
use App\Services\Withdraw;
use App\Structures\WithdrawResult;

class QiwiWalletRepository implements Contract {

    function __construct() {
    }

    public function all() {
        $types = QiwiWalletType::with('wallets')->get();
        foreach ($types as $type) {
            foreach ($type['wallets'] as &$wallet) {
                $wallet['settings'] = $wallet->settings;
            }
        }

        return $types;
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
        $wallet = (new QiwiWallet())->where('id', $id)->first();

        return $wallet ?: null;
    }

    public function remove($login) {
        $deleted = QiwiWallet::where("login", $login)->delete();

        return $deleted;
    }

    /**
     * @param $login
     * @param $withdrawType
     * @param $sum
     * @param $comment
     * @param $targetField
     * @param $cardholderName
     * @param $cardholderSurname
     * @return \App\Structures\WithdrawResult
     */
    public function withdraw($login, $withdrawType,
                             $sum, $comment,
                             $targetField, $cardholderName,
                             $cardholderSurname) {
        switch ($withdrawType) {

            case "wallet":
                $result = Withdraw::toQiwiWallet($login, $targetField, "RUB", $sum, $comment);
                break;

            case "card":
                $result = Withdraw::toCreditCard(
                        $login, $targetField,
                        $cardholderName, $cardholderSurname,
                        $sum, "RUB", $comment);
                break;

            case "voucher":
                if ($targetField != "") {
                    $result = Withdraw::activateVoucher($login, $targetField);
                } else {
                    $result = Withdraw::purchaseVoucher($login, $sum);
                }

                break;

            default:
                $result = new WithdrawResult();
        }

        return json_encode($result);
    }

    public function autoWithdraw($login) {
        $aw = new Autowithdraw($login, true);

        try {
            $status = $aw->autoWithdraw() ? 200 : 400;
        } catch (\Exception $ex) {
            $status = 500;
        }

        return response()->json([], $status);
    }

    public function activateVoucher($login, $code) {
        return json_encode(Withdraw::activateVoucher($login, $code));
    }

    public function createVoucher($login, $amount) {
        return json_encode(Withdraw::purchaseVoucher($login, $amount));
    }

    public function updateBalance($login, $postAction = true) {
        $balance = QiwiGeneralHelper::getBalance($login);
        $wallet = QiwiWallet::findByLogin($login);
        if ($balance != null) $wallet->updateBalance($balance);
        if ($postAction) $wallet->postUpdateRoutine();

        return response()->json(['balance' => $balance], 200);
    }

    public function updateIncome($login, $postAction = true) {
        $job = new UpdateIncomeJob($login);
        dispatch($job);
        //        $monthIncome = QiwiGeneralHelper::getMonthIncome($login);
        //        $wallet = QiwiWallet::findByLogin($login);
        //        $wallet->updateIncome($monthIncome);
        //        if ($postAction) $wallet->postUpdateRoutine();

        return response()->json([], 200);
    }

    /**
     * {@inheritdoc}
     */
    public function reportFor($query, $login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $transactionProcessor = new TransactionProcessor(
                $qiwi->reportForDateRange(
                        $query['start'],
                        $query['end'],
                        $query['page']));

        return $transactionProcessor->getTransactions();
    }

    public function reportIncomeExpenditure($query, $login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $totals = $qiwi->getTotals($query['start'], $query['end']);

        return [
                'income' => $totals['income'],
                'expenditure' => $totals['expenditure'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function settings($login) {
        $wallet = QiwiWallet::findByLogin($login);

        $settings['wallet'] = $wallet;
        $settings['walletSettings'] = $wallet->settings;
        $settings['walletTypes'] = QiwiWalletType::all();
        $settings['autoWithdrawTypes'] = AutowithdrawTypes::all();
        $settings['proxy'] = Proxy::find($wallet->proxy_id);

        return $settings;
    }

    public function createWallet($data) {
        if (QiwiWallet::walletExistsByName($data->name)) {
            $tmpWallet = QiwiWallet::findByName($data->name);
            $result['status'] = "failure";
            $result['message'] = "Кошелек с таким именем уже есть в системе. Привязан к номеру " . $tmpWallet->login;

            return $result;
        }
        if (QiwiWallet::walletExists($data->login)) {
            $tmpWallet = QiwiWallet::findByLogin($data->login);
            $result['status'] = "failure";
            $result['message'] = "Этот номер уже внесен в систему. Имеет имя " . $tmpWallet->name;

            return $result;
        }

        $request = $data;

        // create new proxy
        $proxyRepository = new ProxyRepository();
        $proxyRepository->create($request['proxy']);
        $proxy = $proxyRepository->getLast();

        // try to login to new wallet
        $qiwiControl = QiwiGeneralHelper::getQiwiControlObject(
                $request->login, $request->password,
                $request->useProxy, $request['proxy']);

        //        dump(['logged' => $qiwiControl->loggedIn]);
        //        dump(['qiwi' => $qiwiControl]);
        //        dump(['balance' => $qiwiControl->getBalance()]);
//        echo "<pre>";
//        print_r($qiwiControl);
//        echo "</pre>";
//        echo "<pre>";
//        echo "Balance<br>";
//        print_r($qiwiControl->loadBalance());
//        echo "</pre>";
        if (!$qiwiControl->login() && ($qiwiControl->loadBalance() == false)) {
            $result['status'] = "failure";
            //            $result['message'] = "Кошелек не найден в системе Qiwi " . $qiwiControl->getLastError();
            $result['message'] = "Кошелек не найден в системе Qiwi";
            

            return $result;
        };

        // fetch balance from qiwi with library
        $request->typeId = (new QiwiWalletType())->findByType($request->type)->id;
        //                $request->balance = $qiwiControl->loadBalance()['RUB'];
        $request->balance = 0;

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        //        $request->monthIncome = $request->balance;
        $request->monthIncome = 0;

        // add new wallet to DB with proxy or not

        $wallet = (new Qiwiwallet())::insertWallet($data, $proxy->id);

        // create a general settings and security settings in DB
        $settings = new QiwiWalletSettings([
                'wallet_id' => $wallet->id,
                "autoWithdrawal_type_id" => 1
        ]);

        $securitySettings = new QiwiWalletSecuritySettings(['wallet_id' => $wallet->id]);
        $wallet->settings()->save($settings);
        $wallet->securitySettings()->save($securitySettings);

        $result['code'] = "200";
        $result['status'] = "success";
        $result['message'] = "Кошелек успешно добавлен.";

        return $result;
    }

    public function updateSettings($data) {
        // update wallet table
        $wallet = $this->updateWallet($data->login, $data->name, $data->walletActive,
                $data->walletType, $data->useProxy);

        // update proxy for wallet
        if ($wallet->use_proxy) {
            (new Proxy())->find($wallet->proxy_id)->update($data->proxy);
        }

        // update settings in settings table
        $this->updateWalletSettings($data, $data->login);
    }

    public function updateSecurity($data) {
        $login = $data->login;
        $action = $data->action;
        $options = $data->options;

        $result = null;

        switch ($action) {
            case "SMS_CONFIRMATION":
                if (isset($options['code'])) {
                    $result = QiwiSecurityHelper::userConfirmBySMS($login, "SMS_CONFIRMATION", $options['token'], $options['code']);
                } else {
                    $result = QiwiSecurityHelper::smsConfirmation($login, $options['value']);
                }
                break;

            case "EMAIL":
                if (isset($options['email'])) {
                    $result = QiwiSecurityHelper::emailBinding($login, $options['email']);
                } else if (count($options) == 0) {
                    $result = QiwiSecurityHelper::emailFetchToken($login);
                } else {
                    $result = QiwiSecurityHelper::emailUnbinding($login, $options['code'], $options['token']);
                }
                break;

            case "CALL_CONFIRMATION":
                // switch off/on
                if (isset($options['code'])) {
                    $result = QiwiSecurityHelper::userConfirmBySMS($login, "CALL_CONFIRMATION", $options['token'], $options['code']);
                    //                    $result = QiwiSecurityHelper::setSecurityAttribute($login, "CALL_CONFIRMATION", true);
                } else {
                    $result = QiwiSecurityHelper::callConfirm($login, $options['value']);
                }
                break;

            case "TOKEN":
            case "PIN":
            case "SMS_PAYMENT":
                $result = QiwiSecurityHelper::setSecurityAttribute($login, $action, $options['value']);
                break;
        }

        return json_encode($result);
    }

    public function fetchSecurity($login) {
        return QiwiSecurityHelper::getSecuritySettings($login);
    }

    public function getIdentification($login) {
        return QiwiIdentificationHelper::getIdentification($login);
    }

    public function updateIdentification($data) {
        return QiwiIdentificationHelper::updateIdentification($data);
    }

    /**
     * @param $action string
     * @param $wallets array
     * @return mixed|bool
     */
    public function massAction($action, $wallets) {
        $map = new MassActionProcessor($action, $wallets);


        return $map->execute() === false ? "FAIL" : "OK";
    }

    private function updateWalletSettings($data, $login) {
        (new QiwiWalletSettings)->updateSettings($data, $login);
    }

    private function updateWallet($login, $name, $isActive, $walletType, $useProxy) {
        $wallet = (new QiwiWallet())->findByLogin($login);
        $wallet->updateWallet($name, $isActive, $walletType, $useProxy);

        return $wallet;
    }
}