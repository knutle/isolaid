<?php

namespace Knutle\IsoView\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'install';

    protected $description = 'Installs package.';

    public function handle()
    {
        $this->comment('Publishing routes...');

        $this->callSilently('vendor:publish', [
            '--tag' => 'isoview-routes',
        ]);

        $this->output->success('Install successful!');

        $this->comment('Run ./vendor/bin/isoview to list available commands.');
    }
}
