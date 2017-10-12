<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AutowithdrawTypes
 *
 * @property int $id
 * @property string $type
 * @property string $slug
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutowithdrawTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutowithdrawTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutowithdrawTypes whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutowithdrawTypes whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AutowithdrawTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AutowithdrawTypes extends Model {
    protected $table = "autowithdraw_types";

    public function findBySlug($slug) {
        return $this->where('slug', $slug)->first();
    }


    public function isEveryXMinutes() {
        return $this->slug == "every_x_minutes";
    }

    public function isAfterBalanceUpdate() {
        return $this->slug == "after_balance_update";
    }

    public function isManually() {
        return $this->slug == "manually";
    }
}
