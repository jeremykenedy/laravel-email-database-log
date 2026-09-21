<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\View;

use Illuminate\Contracts\Config\Repository;
use Illuminate\View\View;

class DashboardComposer
{
    private Repository $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function compose(View $view): void
    {
        $theme = $this->config->get('laravel-email-database-log.theme', 'system');
        $view->with([
            'framework' => $this->config->get('laravel-email-database-log-ui.framework', 'standalone'),
            'theme'     => in_array($theme, ['light', 'dark', 'system'], true) ? $theme : 'system',
        ]);
    }
}
