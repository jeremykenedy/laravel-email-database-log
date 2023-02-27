<?php

namespace jeremykenedy\LaravelEmailDatabaseLog;

use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogEventServiceProvider;

class LaravelEmailDatabaseLogServiceProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(LaravelEmailDatabaseLogEventServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Database/Migrations' => database_path('migrations'),
            ], 'laravel-email-database-log-migration');
        }
    }
}
