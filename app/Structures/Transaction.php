<?php

namespace App\Structures;

class Transaction {
    public $date;
    public $time;
    public $transaction;
    public $status;
    public $provider;
    public $opNumber;
    public $comment;
    public $amount;
    public $sign;
    public $commission;
    public $currency = "руб.";
}