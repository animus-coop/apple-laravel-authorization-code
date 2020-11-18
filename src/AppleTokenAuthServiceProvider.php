<?php

namespace AnimusCoop\AppleTokenAuth;

use AnimusCoop\AppleTokenAuth\Classes\AppleAuth;
use Illuminate\Support\ServiceProvider;

class AppleTokenAuthServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AppleAuth::class, function () {
            return new AppleAuth($configData = []);
        });
    }
}
