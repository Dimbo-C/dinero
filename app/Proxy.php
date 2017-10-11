<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Proxy
 *
 * @property int $id
 * @property string|null $host
 * @property string|null $port
 * @property string|null $login
 * @property string|null $password
 * @property string|null $country
 * @property string|null $status
 * @property string $type
 * @property string|null $using_type
 * @property int|null $price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proxy whereUsingType($value)
 * @mixin \Eloquent
 */
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
