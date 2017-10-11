<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\WalletSettings
 *
 * @mixin \Eloquent
 */
	class WalletSettings extends \Eloquent {}
}

namespace App{
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
	class Proxy extends \Eloquent {}
}

namespace App{
/**
 * App\QiwiWallet
 *
 * @property int $id
 * @property int $type_id
 * @property int $use_proxy
 * @property int|null $proxy_id
 * @property string $name
 * @property string $login
 * @property string $password
 * @property string|null $balance
 * @property string|null $month_income
 * @property int $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\QiwiWalletType $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereMonthIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\QiwiWallet whereUseProxy($value)
 * @mixin \Eloquent
 */
	class QiwiWallet extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property int|null $head_id
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User|null $head
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $staff
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereHeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace App{
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
	class AutowithdrawTypes extends \Eloquent {}
}

namespace App{
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
	class QiwiWalletType extends \Eloquent {}
}

