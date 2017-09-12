<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutowithdrawTypes extends Model {
    protected $table = "autowithdraw_types";

    public function findBySlug($slug){
        return $this->where('slug', $slug)->first();
    }
}
