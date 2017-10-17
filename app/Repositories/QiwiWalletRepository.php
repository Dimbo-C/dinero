<?php

namespace App\Repositories;

use App\AutowithdrawTypes;
use App\Cazzzt\Qiwi\QiwiControl\QIWIControl;
use App\Contracts\Repositories\QiwiWalletRepository as Contract;
use App\Helpers\QiwiGeneralHelper;
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
use Illuminate\Support\Facades\Log;

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
        $aw = new Autowithdraw($login);

        return ($aw->autoWithdraw(1, true)) ? 1 : 0;
    }

    public function activateVoucher($login, $code) {
        return json_encode(Withdraw::activateVoucher($login, $code));
    }

    public function createVoucher($login, $amount) {
        return json_encode(Withdraw::purchaseVoucher($login, $amount));
    }

    /**
     * {@inheritdoc}
     */
    public function updateBalanceAndIncome($login) {
        $balance = QiwiGeneralHelper::getBalance($login);
        $monthIncome = $balance;

        $this->staticWallet->updateBalanceAndIncome($login, $balance, $monthIncome);

        return [
                "monthIncome" => $monthIncome,
                "balance" => $balance,
                "options" => [],
        ];
    }

    public function updateBalance($login) {
        $balance = QiwiGeneralHelper::getBalance($login);
        $wallet = QiwiWallet::findByLogin($login);

        $this->staticWallet->updateBalance($login, $balance);

        return $balance;
    }

    public function updateIncome($login) {
        $monthIncome = QiwiGeneralHelper::getMonthIncome($login);
        $this->staticWallet->updateIncome($login, $monthIncome);

        return $monthIncome;
    }

    /**
     * {@inheritdoc}
     */
    public function reportFor($query, $login) {
        $qiwi = QiwiGeneralHelper::getQiwiInstance($login);
        $start = $query['start'];
        $end = $query['end'];
        $transactionProcessor = new TransactionProcessor($qiwi->reportForDateRange($start, $end));
        $totals = $qiwi->getTotals($start, $end);

        return [
                'history' => $transactionProcessor->getTransactions(),
                'income' => $totals['income'],
                'outcome' => $totals['expenditure'],
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
        $settings['id'] = $wallet->id;
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
        if (!$qiwiControl->login()) {
            $result['status'] = "failure";
            $result['message'] = "Кошелек не найден в системе Qiwi " . $qiwiControl->getLastError();

            return $result;
        };
        //        dd($proxy->id);

        // fetch balance from qiwi with library
        $request->typeId = (new QiwiWalletType())->findByType($request->type)->id;
        $request->balance = $qiwiControl->loadBalance()['RUB'];

        // get income data from qiwi (lib returns empty array for now, so it is a dummy)
        $request->monthIncome = $request->balance;

        // add new wallet to DB with proxy or not
        $wallet = new Qiwiwallet();
        $wallet->name = $data->name;
        $wallet->login = $data->login;
        $wallet->password = $data->password;
        $wallet->is_active = $data->isActive;
        $wallet->type_id = $data->typeId;
        $wallet->balance = $data->balance;
        $wallet->month_income = $data->monthIncome;
        $wallet->use_proxy = $data->useProxy;
        $wallet->proxy_id = $proxy->id;
        $wallet->save();

        // create a general settings and security settings in DB
        $settings = new QiwiWalletSettings(['wallet_id' => $wallet->id]);
        $securitySettings = new QiwiWalletSecuritySettings(['wallet_id' => $wallet->id]);
        $wallet->settings()->save($settings);
        $wallet->securitySettings()->save($securitySettings);

        $result['status'] = "success";
        $result['message'] = "Кошелек успешно добавлен.";

        return $result;
    }

    public function updateSettings($data) {
        // update wallet table
        $wallet = $this->updateWallet($data->login, $data->name, $data->walletActive, $data->walletType, $data->useProxy);

        // update proxy for wallet
        if ($wallet->use_proxy) {
            Proxy::find($wallet->proxy_id)->update($data->proxy);
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
                    $result = QiwiGeneralHelper::userConfirmBySMS($login, $options['token'], $options['code']);
                } else {

                    $result = QiwiGeneralHelper::smsConfirmation($login, $options['value']);
                }
                break;
        }

        return json_encode($result);
    }

    public function fetchSecurity($login) {
        return QiwiGeneralHelper::getSecuritySettings($login);
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

    private function findByLogin($login) {
        $wallet = QiwiWallet::where('login', $login)->first();

        return $wallet ?: null;
    }

    private function updateWalletSettings($data, $login) {
        $wallet = $this->staticWallet->findByLogin($login);
        (new QiwiWalletSettings)->updateWithData($data, $wallet->id);
    }

    private function updateWallet($login, $name, $isActive, $walletType, $useProxy) {
        $typeId = (new QiwiWalletType())->findByType($walletType)->id;

        $wallet = $this->findByLogin($login);

        $wallet->name = $name;
        $wallet->is_active = $isActive;
        $wallet->type_id = $typeId;
        $wallet->use_proxy = $useProxy;

        $wallet->save();

        return $wallet;
    }


}