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
     * Update balance of specific wallet
     * and return balance and income for the current month
     * @param $login
     * @return mixed
     */
    public function updateBalanceAndIncome($login);

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
