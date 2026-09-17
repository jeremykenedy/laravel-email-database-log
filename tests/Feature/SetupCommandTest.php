<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\TestCase;

class SetupCommandTest extends TestCase
{
    protected $sandbox;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sandbox = sys_get_temp_dir().'/email-log-'.bin2hex(random_bytes(8));
        $files = new Filesystem;
        foreach (['config', 'database/migrations', 'resources/views', 'public'] as $path) {
            $files->ensureDirectoryExists($this->sandbox.'/'.$path);
        }
        $this->app->setBasePath($this->sandbox);
        $this->app->useConfigPath($this->sandbox.'/config');
        $this->app->useDatabasePath($this->sandbox.'/database');
        $this->app->instance('path.public', $this->sandbox.'/public');
        if (method_exists($this->app, 'usePublicPath')) {
            $this->app->usePublicPath($this->sandbox.'/public');
        }
        $this->app->getProvider(LaravelEmailDatabaseLogServiceProvider::class)->boot();
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->sandbox);
        parent::tearDown();
    }

    protected function runCommand(string $name, array $options = []): int
    {
        return Artisan::call($name, array_merge(['--no-interaction' => true], $options));
    }

    protected function settings(): array
    {
        return require config_path('laravel-email-database-log-ui.php');
    }

    public function test_non_interactive_install_preserves_logging_only_default(): void
    {
        $this->assertSame(0, $this->runCommand('email-log:install'));
        $this->assertFalse($this->settings()['enabled']);
        $this->assertFileExists(database_path('migrations/2023_02_26_001638_create_email_log.php'));
        $this->assertDirectoryDoesNotExist(public_path('vendor/email-log'));
    }

    public function test_install_can_select_each_framework_and_publish_views_and_assets(): void
    {
        foreach (['standalone', 'bootstrap5', 'tailwind'] as $framework) {
            $this->assertSame(0, $this->runCommand('email-log:install', ['--framework' => $framework, '--publish-views' => true]));
            $this->assertSame($framework, $this->settings()['framework']);
            $this->assertTrue($this->settings()['enabled']);
            $this->assertFileExists(resource_path('views/vendor/email-log/index.blade.php'));
            $this->assertFileExists(public_path('vendor/email-log/css/dashboard.css'));
            $this->assertFileExists(public_path('vendor/email-log/js/theme.js'));
        }
    }

    public function test_update_retains_current_selection_and_custom_configuration(): void
    {
        config(['laravel-email-database-log-ui' => ['enabled' => true, 'framework' => 'tailwind', 'ui_kit' => false]]);
        $original = "<?php\nreturn ['path' => 'custom-mail', 'middleware' => ['web', 'admin']];\n";
        file_put_contents(config_path('laravel-email-database-log.php'), $original);
        $this->assertSame(0, $this->runCommand('email-log:update'));
        $this->assertSame('tailwind', $this->settings()['framework']);
        $this->assertSame($original, file_get_contents(config_path('laravel-email-database-log.php')));
        $this->assertSame([], glob(database_path('migrations/*.php')));
    }

    public function test_update_can_switch_frameworks_or_disable_dashboard(): void
    {
        $this->assertSame(0, $this->runCommand('email-log:update', ['--framework' => 'bootstrap5']));
        $this->assertSame('bootstrap5', $this->settings()['framework']);
        $this->assertSame(0, $this->runCommand('email-log:update', ['--framework' => 'none']));
        $this->assertFalse($this->settings()['enabled']);
    }

    public function test_customized_views_are_only_replaced_with_force_views(): void
    {
        $this->runCommand('email-log:install', ['--publish-views' => true]);
        $path = resource_path('views/vendor/email-log/index.blade.php');
        file_put_contents($path, 'Custom view');
        $this->runCommand('email-log:update', ['--publish-views' => true]);
        $this->assertSame('Custom view', file_get_contents($path));
        $this->runCommand('email-log:update', ['--force-views' => true]);
        $this->assertStringContainsString('@extends', file_get_contents($path));
    }

    public function test_existing_renamed_migration_is_never_duplicated_or_overwritten(): void
    {
        $path = database_path('migrations/2020_01_01_000000_create_email_log.php');
        file_put_contents($path, 'Existing migration');
        $this->runCommand('email-log:install');
        $this->assertSame('Existing migration', file_get_contents($path));
        $this->assertCount(1, glob(database_path('migrations/*.php')));
    }

    public function test_invalid_options_fail_before_writing_files(): void
    {
        foreach ([['--framework' => '../../bad'], ['--ui-kit' => true, '--without-ui-kit' => true]] as $options) {
            $this->assertSame(1, $this->runCommand('email-log:install', $options));
            $this->assertFileDoesNotExist(config_path('laravel-email-database-log-ui.php'));
            $this->assertSame([], glob(database_path('migrations/*.php')));
        }
    }

    public function test_missing_ui_kit_is_reported_without_installing_dependencies(): void
    {
        if (class_exists('Jeremykenedy\\LaravelUiKit\\Providers\\UiKitServiceProvider')) {
            $this->markTestSkipped('This test covers installations without UI Kit.');
        }
        $this->assertSame(1, $this->runCommand('email-log:install', ['--framework' => 'bootstrap5', '--ui-kit' => true]));
        $this->assertFileDoesNotExist(config_path('laravel-email-database-log-ui.php'));
        $this->assertStringContainsString('Install jeremykenedy/laravel-ui-kit separately', Artisan::output());
    }

    public function test_optional_integration_can_be_disabled(): void
    {
        config(['laravel-email-database-log-ui.ui_kit' => true]);
        $this->assertSame(0, $this->runCommand('email-log:update', ['--without-ui-kit' => true]));
        $this->assertFalse($this->settings()['ui_kit']);
    }

    public function test_interactive_install_offers_framework_and_optional_ui_kit(): void
    {
        $this->artisan('email-log:install')
            ->expectsChoice('Dashboard framework', 'bootstrap5', ['none', 'standalone', 'bootstrap5', 'tailwind'])
            ->expectsConfirmation('Use the optional, separately installed Laravel UI Kit?', 'no')
            ->assertExitCode(0);
    }
}
