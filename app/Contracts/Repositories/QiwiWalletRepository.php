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
     * Update balance of specific wallet and return current value
     * @param $login
     * @param $postAction boolean if post action should be executed
     * @return mixed
     */
    public function updateBalance($login, $postAction = true);

    /**
     * Update income of specific wallet and return current value
     * @param $login
     * @param $postAction boolean if post action should be executed
     * @return mixed
     */
    public function updateIncome($login, $postAction = true);


    /**
     * Get report for specific DateRange
     * @param $query
     * @param $login
     * @return mixed
     */
    public function reportFor($query, $login);

    /**
     * Get settings of specific wallet
     * @param $login string wallet
     * @return mixed
     */
    public function settings($login);

    /**
     * Add new wallet
     * @param $data
     * @return mixed
     */
    public function createWallet($data);

    /**
     * Save settings
     * @param $data
     * @return mixed
     */
    public function updateSettings($data);

}
