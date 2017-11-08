<?php

namespace App\Processors;

use App\Helpers\MoneyHelper;
use App\Structures\Transaction;

define("QIWI_TRANSACTION_MODE_IN", 1);
define("QIWI_TRANSACTION_MODE_OUT", 2);
define("QIWI_TRANSACTION_MODE_INOUT", 3);

class TransactionProcessor {
    private $transactions;
    private $commissionEmptyMessage = "Отсутствует";
    private $commentEmptyMessage = "Отсутствует";


    /**
     * TransactionProcessor constructor.
     * @param array $transactions
     * @param null $commissionEmptyMessage
     * @param null $commentEmptyMessage
     */
    function __construct(array $transactions, $commissionEmptyMessage = null, $commentEmptyMessage = null) {
        if ($commissionEmptyMessage != null) $this->commissionEmptyMessage = $commissionEmptyMessage;
        if ($commentEmptyMessage != null) $this->commentEmptyMessage = $commentEmptyMessage;

        $this->transactions = $transactions;
        $this->tidyTransactions();
    }

    public function getTransactions() {
        return $this->transactions;
    }

    public function getIncome() {
        $transactions = $this->filterTransactions(QIWI_TRANSACTION_MODE_IN);
        $sum = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->status == "error") continue;
            $sum += $transaction->amount;
        }

        return $sum;
    }

    public function getOutcome() {
        $transactions = $this->filterTransactions(QIWI_TRANSACTION_MODE_OUT);
        $sum = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->status == "error") continue;
            $sum += $transaction->amount;
        }

        return $sum;
    }

    // initially tidy transactions after they come in
    private function tidyTransactions() {
        $this->detectCurrency();
        $this->stripCurrency();
        $this->defaulterizeComments();
        $this->findCommissions();

        // ... probably something else to tidy transactions
    }


    // get only specific transactions (in or out, or both)
    private function filterTransactions($mode) {
        $result = [];
        foreach ($this->transactions as $transaction) {
            switch ($mode) {
                case QIWI_TRANSACTION_MODE_IN:
                    if ($this->isIncome($transaction)) $result[] = $transaction;
                    break;
                case QIWI_TRANSACTION_MODE_OUT:
                    if (!$this->isIncome($transaction)) $result[] = $transaction;
                    break;
                default:
                    $result[] = $transaction;
                    break;
            }
        }

        return $result;
    }

    // get only money value from amount string
    private function stripCurrency() {
        foreach ($this->transactions as &$transaction) {
            $transaction->amount = MoneyHelper::moneyToFloat($transaction->amount);
        }
    }

    // get currency string from amount
    private function detectCurrency() {
        foreach ($this->transactions as &$transaction) {
            $transaction->currency = MoneyHelper::getCurrency($transaction->amount);
        }
    }

    // put '-' as comment if there is none
    private function defaulterizeComments() {
        foreach ($this->transactions as &$transaction) {
            $transaction->comment = $transaction->comment == ""
                    ? $this->commentEmptyMessage
                    : $transaction->comment;
        }
    }

    // find commission payments and bind them to their real payments
    private function findCommissions() {
        foreach ($this->transactions as &$transaction) {
            foreach ($this->transactions as $key => $subtransaction) {
                if (strpos($subtransaction->comment, $transaction->transaction) !== false) {
                    $transaction->commission = $subtransaction->amount . " " . $subtransaction->currency;
                    unset($this->transactions[$key]);
                }
            }
            if ($transaction->commission == "") $transaction->commission = $this->commissionEmptyMessage;
        }
        $this->transactions = array_values($this->transactions);
    }

    /**
     * @param $transaction Transaction
     * @return bool
     */
    private function isIncome($transaction) {
        return $transaction->sign == "+";
    }


}