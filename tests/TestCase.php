<?php

namespace Nikservik\SimpleSupport\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Lorisleiva\Actions\ActionServiceProvider;
use Nikservik\SimpleSupport\SimpleSupportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Nikservik\\SimpleSupport\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SimpleSupportServiceProvider::class,
            ActionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('app.fallback_locale', 'ru');
        $app['config']->set('simple-support.features', [
            'user-can-send-message',
            'user-can-update-message',
            'user-can-delete-message',
            'send-notifications-to-telegram',
            'register-api-routes',
            'autoload-migrations',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
