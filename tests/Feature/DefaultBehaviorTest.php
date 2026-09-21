<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\TestCase;

class DefaultBehaviorTest extends TestCase
{
    public function test_upgrading_registers_no_dashboard_routes_by_default(): void
    {
        $this->assertFalse(config('laravel-email-database-log-ui.enabled'));
        $this->assertFalse(Route::has('email-log.index'));
        $this->assertFalse(Route::has('email-log.show'));
        $this->get('/email-log')->assertNotFound();
    }

    public function test_original_migration_publish_tag_and_filename_are_preserved(): void
    {
        $paths = ServiceProvider::pathsToPublish(null, 'laravel-email-database-log-migration');
        $this->assertContains(database_path('migrations'), array_values($paths));
        $source = array_keys($paths)[0];
        $this->assertFileExists($source.'/2023_02_26_001638_create_email_log.php');
    }

    public function test_console_commands_are_registered(): void
    {
        $commands = $this->app->make(Kernel::class)->all();
        $this->assertArrayHasKey('email-log:install', $commands);
        $this->assertArrayHasKey('email-log:update', $commands);
    }
}
