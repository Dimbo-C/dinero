<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutowithdrawTypes extends Model {
    protected $table = "autowithdraw_types";

    public function findBySlug($slug) {
        return $this->where('slug', $slug)->first();
    }

    public function isEveryXMinutes() {
        return $this->slug = "every_x_minutes";
    }

    public function isAfterBalanceUpdate() {
        return $this->slug = "after_balance_update";
    }

    public function isManually() {
        return $this->slug = "manually";
    }
}
