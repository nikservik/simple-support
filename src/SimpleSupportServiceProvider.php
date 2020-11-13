<?php

namespace Nikservik\SimpleSupport;

use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class SimpleSupportServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        // User::class => UserPolicy::class,
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/simple-support.php', 'simple-support');
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'simplesupport');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadViewsFrom(__DIR__.'/../views', 'simplesupport');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->registerPolicies();
        $this->publishes([
            __DIR__.'/../config/simple-support.php' => config_path('simple-support.php')
        ], 'config');
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/simple-support'),
        ], 'views');
        $this->publishes([
            __DIR__.'/../lang' => resource_path('lang/vendor/simple-support'),
        ], 'translations');
    }
}
