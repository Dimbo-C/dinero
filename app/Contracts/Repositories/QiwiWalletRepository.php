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
    public function createWallet($data);


    /**
     * Get wallet data from qiwi
     * @param $login
     * @return mixed
     */
    public function updateBalanceAndIncome($login);

    public function settings($login);

    /**
     * Save settings
     * @param $data
     * @return mixed
     */
    public function updateSettings($data);

    /**
     * withdraw moneyz
     * @param $cardNumber
     * @param $firstName
     * @param $lastName
     * @param $sum
     * @param $cur
     * @param $comment
     * @return mixed
     */
    public function withdrawMoney($cardNumber, $firstName, $lastName, $sum, $cur, $comment);

}
