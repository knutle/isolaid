<?php

namespace Knutle\Isolaid\Commands;

use Illuminate\Console\Command;
use function storage_path;
use Symfony\Component\Process\Process;

class IsolaidLogsCommand extends Command
{
    protected $signature = 'isolaid:logs {--dir : Navigate to logs directory without tailing}';

    protected $description = 'Tails default log file from Laravel stub app.';

    public function handle(): int
    {
        if ($this->option('dir')) {
            $this->line(storage_path('logs'));

            return static::SUCCESS;
        }

        $filePath = storage_path('logs/laravel.log');

        $this->line('Tailing '.$filePath);

        $process = new Process(['tail', '-f', $filePath]);

        $process->setTimeout(null);

        $process->start(function ($type, $buffer) {
            $this->line("$buffer");
        });

        $process->wait();

        return static::SUCCESS;
    }
}
