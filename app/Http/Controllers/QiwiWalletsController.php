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

    /**
     * @return mixed
     */
    public function all() {
        return $this->wallet->all();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function move(Request $request) {
        return $this->wallet->moveWalletsTo($request->wallets, $request->to);
    }

    public function store(Request $request) {
        return $this->wallet->storeWallet($request);
    }

    public function update(Request $request) {
        return $this->wallet->findInQiwi($request);
    }

    public function test(Request $request) {

    }
}