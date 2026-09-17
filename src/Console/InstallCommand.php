<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Console;

class InstallCommand extends SetupCommand
{
    protected $signature = 'email-log:install
        {--framework= : none, standalone, bootstrap5, or tailwind}
        {--ui-kit : Use the separately installed Laravel UI Kit}
        {--without-ui-kit : Disable Laravel UI Kit components}
        {--publish-views : Publish missing Blade views}
        {--force-views : Replace published package views}';

    protected $description = 'Install email logging and optionally configure its dashboard';
}
