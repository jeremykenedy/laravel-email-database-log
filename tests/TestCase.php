<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests;

use jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations()
    {
        // Point to the package migrations so Testbench runs them
        $this->loadMigrationsFrom(__DIR__.'/../src/Database/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelEmailDatabaseLogServiceProvider::class,
        ];
    }
}
