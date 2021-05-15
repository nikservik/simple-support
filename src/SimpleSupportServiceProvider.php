<?php

namespace Nikservik\SimpleSupport;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SimpleSupportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/simple-support.php', 'simple-support');
    }

    public function boot()
    {
        $this->loadMigrations();
        $this->registerRoutes();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/simple-support.php' => config_path('simple-support.php'),
        ], 'simple-support-config');
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations'),
        ], 'simple-support-migrations');
    }

    protected function loadMigrations(): void
    {
        if (in_array('autoload-migrations', Config::get('simple-support.features'))) {
            $this->loadMigrationsFrom(__DIR__.'/../migrations');
        }
    }

    protected function registerRoutes(): void
    {
        if (in_array('register-api-routes', Config::get('simple-support.features'))) {
            $this->loadRoutesFrom(__DIR__.'/../routes.php');
        }
    }
}
