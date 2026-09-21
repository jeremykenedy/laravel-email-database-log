<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Console;

use Illuminate\Filesystem\Filesystem;

class SwitchCommand extends UpdateCommand
{
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
        $this->setName('email-log:switch');
        $this->setDescription('Switch dashboard frameworks using explicit options');
    }

    public function handle(): int
    {
        if ($this->option('framework') === null) {
            $this->error('Provide --framework=none, standalone, bootstrap5, or tailwind.');

            return 1;
        }

        return parent::handle();
    }
}
