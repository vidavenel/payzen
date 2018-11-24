<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 16:36
 */

namespace Vidavenel\Payzen;

use Illuminate\Support\ServiceProvider;

class PayzenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'payzen');
        $this->publishes([
            __DIR__.'/config/payzen.php' => config_path('payzen.php')
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/payzen.php', 'payzen'
        );
        $this->app->bind(PayzenService::class, function ($app) {
            return new PayzenService(config('payzen.site_id'), config('payzen.mode'));
        });
    }
}