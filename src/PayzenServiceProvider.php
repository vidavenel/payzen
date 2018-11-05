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
        $this->loadViewsFrom(__DIR__.'/views', 'payzen');
    }

    public function register()
    {
        $this->app->singleton('payzen', function ($app) {
            return new PayzenService('123');
        });
    }
}