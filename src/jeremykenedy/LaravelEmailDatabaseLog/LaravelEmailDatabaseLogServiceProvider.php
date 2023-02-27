<?php

namespace jeremykenedy\LaravelEmailDatabaseLog;

use Illuminate\Support\ServiceProvider;

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
            $this->publishes(
                [
                __DIR__.'/../../database/migrations' => database_path('migrations'),
                ], 'laravel-email-database-log-migration'
            );
        }
    }
}
