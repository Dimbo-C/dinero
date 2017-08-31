<?php

namespace App\Contracts\Repositories;

interface QiwiWalletRepository {
    /**
     * Получаем все кошельки киви.
     * @return mixed
     */
    public function all();

    /**
     * @param $wallets
     * @param $to
     * @return mixed
     */
    public function moveWalletsTo($wallets, $to);

    /**
     * Get the wallet with the given ID.
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Add new wallet
     * @param $data
     * @return mixed
     */
    public function storeWallet($data);

    /**
     * Get wallet data from qiwi
     * @param $data
     * @return mixed
     */
    public function findInQiwi($data);


}
