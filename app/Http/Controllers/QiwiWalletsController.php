<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QiwiWalletRepository;
use Illuminate\Http\Request;

class QiwiWalletsController extends Controller {
    /**
     * @var QiwiWalletRepository
     */
    protected $wallet;

    /**
     * QiwiWalletsController constructor.
     * @param QiwiWalletRepository $wallet
     */
    public function __construct(QiwiWalletRepository $wallet) {
        $this->wallet = $wallet;
    }

    public function all() {
        return $this->wallet->all();
    }

    public function move(Request $request) {
        return $this->wallet->moveWalletsTo($request->wallets, $request->to);
    }

    public function store(Request $request) {
        return $this->wallet->createWallet($request);
    }

    public function remove($login) {
        return $this->wallet->remove($login);
    }

    public function updateBalanceAndIncome(Request $request) {
        return $this->wallet->updateBalanceAndIncome($request->login);
    }

    public function withdraw(Request $request) {
        return $this->wallet->withdraw(
                $request->login,
                $request->withdrawType,
                $request->sum,
                $request->comment,
                $request->targetField,
                $request->cardholderName,
                $request->cardholderSurname);
    }

    public function saveSettings(Request $request) {
        return $this->wallet->updateSettings($request);
    }

    public function updateWalletSettings(Request $request) {
        return $this->wallet->withdrawMoneys($request);
    }

    public function massAction(Request $request) {
        return $this->wallet->massAction($request->action, $request->wallets);
    }

    public function test(Request $request) {

    }
}