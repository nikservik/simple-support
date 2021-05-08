<?php

namespace Nikservik\SimpleSupport;

use Illuminate\Support\ServiceProvider;

class SimpleSupportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/simple-support.php', 'simple-support');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->publishes([
            __DIR__.'/../config/simple-support.php' => config_path('simple-support.php'),
        ], 'simple-support-config');
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations'),
        ], 'simple-support-migrations');
    }
}
