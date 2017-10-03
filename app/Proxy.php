<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model {
    protected $table = "proxies";
    protected $fillable = [
            'host', 'port', 'login',
            'password', 'country', 'status',
            'type', 'using_type', 'price'
    ];

    public function deleteByIds($ids) {
        return $this->destroy($ids);
    }
}
