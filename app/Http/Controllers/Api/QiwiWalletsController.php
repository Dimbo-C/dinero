<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\QiwiWalletRepository;
use App\Http\Controllers\Controller;
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
     * Получаем историю.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(Request $request, $id) {
        return response()->json($this->wallet->reportFor($request->all(), $id));
    }

    /**
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function incomeExpenditure(Request $request, $id) {
        return response()->json($this->wallet->reportIncomeExpenditure($request->all(), $id));
    }

    /**
     * Получаем настройки.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function settings($id) {
        return response()->json($this->wallet->settings($id));
    }
}