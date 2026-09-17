<?php

namespace jeremykenedy\LaravelEmailDatabaseLog;

use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelEmailDatabaseLog\Console\InstallCommand;
use jeremykenedy\LaravelEmailDatabaseLog\Console\UpdateCommand;

class LaravelEmailDatabaseLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'email-log');

        if (config('laravel-email-database-log-ui.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Database/Migrations' => database_path('migrations'),
            ], 'laravel-email-database-log-migration');
            $this->publishes([
                __DIR__.'/../config/laravel-email-database-log.php'    => config_path('laravel-email-database-log.php'),
                __DIR__.'/../config/laravel-email-database-log-ui.php' => config_path('laravel-email-database-log-ui.php'),
            ], 'laravel-email-database-log-config');
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/email-log'),
            ], 'laravel-email-database-log-views');
            $this->publishes([
                __DIR__.'/../resources/css' => public_path('vendor/email-log/css'),
                __DIR__.'/../resources/js'  => public_path('vendor/email-log/js'),
            ], 'laravel-email-database-log-assets');
            $this->commands([InstallCommand::class, UpdateCommand::class]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-email-database-log.php', 'laravel-email-database-log');
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-email-database-log-ui.php', 'laravel-email-database-log-ui');
        $this->app->register(LaravelEmailDatabaseLogEventServiceProvider::class);
    }
}
