<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Console;

class UpdateCommand extends SetupCommand
{
    protected $signature = 'email-log:update
        {--framework= : none, standalone, bootstrap5, or tailwind}
        {--ui-kit : Use the separately installed Laravel UI Kit}
        {--without-ui-kit : Disable Laravel UI Kit components}
        {--publish-views : Publish missing Blade views}
        {--force-views : Replace published package views}';

    protected $description = 'Refresh dashboard assets and optionally switch frameworks or views';
}
