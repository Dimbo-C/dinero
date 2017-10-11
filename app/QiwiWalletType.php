<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\QiwiWalletType
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\QiwiWallet[] $wallets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWalletType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
