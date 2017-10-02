<?php

namespace App\Processors;


use App\QiwiWallet;
use App\QiwiWalletType;
use App\Repositories\QiwiWalletRepository;

class MassActionProcessor {
    private $action;
    private $wallets;
    private $ids;

    function __construct($action, $wallets) {
        $this->action = $action;
        $this->wallets = $wallets;

        foreach ($this->wallets as $wallet) {
            $this->ids[] = $wallet['id'];
        }
    }

    /**
     * Runs a function that corresponds to $action variable
     *
     * @return bool
     */
    public function execute() {
        $method = $this->action;

        return $this->$method();
    }

    private function moveToReceive() {
        return $this->moveTo("receive");
    }

    private function moveToReserve() {
        return $this->moveTo("reserve");
    }

    private function moveToWithdraw() {
        return $this->moveTo("withdraw");
    }

    private function moveToSpent() {
        return $this->moveTo("spent");
    }

    private function moveTo($type) {
        $typeId = (new QiwiWalletType())->findByType($type)['id'];

        try {
            (new QiwiWalletRepository())->moveWalletsTo($this->ids, $typeId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function remove() {
        if (count($this->wallets) === 0) return 0;

        return (new QiwiWallet)->deleteByIds($this->ids);
    }
}