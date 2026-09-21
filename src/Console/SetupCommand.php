<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

abstract class SetupCommand extends Command
{
    public function __construct(private Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $files = $this->files;
        $settings = config('laravel-email-database-log-ui');
        $current = $settings['enabled'] ? $settings['framework'] : 'none';
        $framework = $this->option('framework');
        $uiKit = $this->option('ui-kit');
        $withoutUiKit = $this->option('without-ui-kit');

        if ($framework === null && $this->input->isInteractive()) {
            $framework = $this->choice('Dashboard framework', ['none', 'standalone', 'bootstrap5', 'tailwind'], $current);
            if ($framework !== 'none' && ! $uiKit && ! $withoutUiKit) {
                $uiKit = $this->confirm('Use the optional, separately installed Laravel UI Kit?', $settings['ui_kit']);
                $withoutUiKit = ! $uiKit;
            }
        }

        $framework = $framework ?? $current;
        if (! in_array($framework, ['none', 'standalone', 'bootstrap5', 'tailwind'], true)) {
            $this->error('Framework must be none, standalone, bootstrap5, or tailwind.');

            return 1;
        }
        if ($uiKit && $withoutUiKit) {
            $this->error('Choose either --ui-kit or --without-ui-kit.');

            return 1;
        }
        $settings['enabled'] = $framework !== 'none';
        $settings['framework'] = $framework === 'none' ? $settings['framework'] : $framework;
        $settings['ui_kit'] = $uiKit ? true : ($withoutUiKit ? false : $settings['ui_kit']);
        if ($settings['enabled'] && $settings['ui_kit'] && ! class_exists('Jeremykenedy\\LaravelUiKit\\Providers\\UiKitServiceProvider')) {
            $this->error('Install jeremykenedy/laravel-ui-kit separately, or use --without-ui-kit.');

            return 1;
        }

        $files->ensureDirectoryExists(config_path());
        $config = config_path('laravel-email-database-log.php');
        if (! $files->exists($config)) {
            $files->copy(__DIR__.'/../../config/laravel-email-database-log.php', $config);
        }
        $files->replace(config_path('laravel-email-database-log-ui.php'), "<?php\n\nreturn ".var_export($settings, true).";\n");

        if ($this instanceof InstallCommand && ! $files->glob(database_path('migrations/*_create_email_log.php'))) {
            $this->call('vendor:publish', ['--tag' => 'laravel-email-database-log-migration']);
        }
        if ($settings['enabled']) {
            $this->call('vendor:publish', ['--tag' => 'laravel-email-database-log-assets', '--force' => true]);
        }
        if ($this->option('publish-views') || $this->option('force-views')) {
            $this->call('vendor:publish', [
                '--tag'   => 'laravel-email-database-log-views',
                '--force' => (bool) $this->option('force-views'),
            ]);
        }
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->info('Email log settings saved. Existing application configuration and migrations were preserved.');
        if ($settings['enabled']) {
            $this->line('Define the viewEmailLog gate before opening the dashboard. See docs/dashboard.md.');
            if (in_array($framework, ['bootstrap5', 'tailwind'], true)) {
                $this->line('Set laravel-email-database-log.stylesheet to your compiled framework CSS URL.');
            }
        }
        $this->line('Run pending migrations when ready. Rebuild configuration and route caches during deployment.');

        return 0;
    }
}
