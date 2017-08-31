<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class Dinero
{
    /**
     * Get the default JavaScript variables for Imprinx.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'csrfToken' => csrf_token(),
            'env' => config('app.env'),
            'state' => app(InitialFrontendState::class)->forUser(Auth::user()),
            'userId' => Auth::id(),
            'currencySymbol' => 'руб.',
            'version' => self::$version,
            'updated_at' => self::$updated_at,
        ];
    }

    /**
     * The Site version.
     */
    public static $version = '0.0.1';

    /**
     * The Site update date.
     */
    public static $updated_at = '28.07.2017';
}