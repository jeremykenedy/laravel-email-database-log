<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests;

use Carbon\Carbon;
use jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-09-17 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../src/Database/Migrations');
    }

    protected function getPackageProviders($app)
    {
        $providers = [LaravelEmailDatabaseLogServiceProvider::class];
        if (class_exists('Jeremykenedy\\LaravelUiKit\\Providers\\UiKitServiceProvider')) {
            array_unshift($providers, 'Jeremykenedy\\LaravelUiKit\\Providers\\UiKitServiceProvider');
        }

        return $providers;
    }
}
