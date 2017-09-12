<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QiwiWalletType extends Model {
    protected $table = "qiwi_wallet_types";

    /**
     * Получаем все кошельки данного типа.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets() {
        return $this->hasMany(QiwiWallet::class, 'type_id');
    }

    public function findByType($type) {
        return $this->where("slug", $type)->first();
    }
}
