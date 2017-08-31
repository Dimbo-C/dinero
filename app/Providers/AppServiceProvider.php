<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! class_exists('Dinero')) {
            class_alias('App\Dinero', 'Dinero');
        }

        $this->registerServices();
    }

    protected function registerServices()
    {
        $services = [
            'Contracts\Repositories\ProxyRepository' => 'Repositories\ProxyRepository',
            'Contracts\Repositories\UserRepository' => 'Repositories\UserRepository',
            'Contracts\Repositories\QiwiWalletRepository' => 'Repositories\QiwiWalletRepository',
            'Contracts\InitialFrontendState' => 'InitialFrontendState',
        ];

        foreach ($services as $key => $value) {
            $this->app->singleton('App\\'.$key, 'App\\'.$value);
        }
    }
}
