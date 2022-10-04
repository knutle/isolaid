<?php

namespace Knutle\Isolaid\Commands;

use function blank;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use function in_array;
use Orchestra\Testbench\Console\Commander;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

class IsolaidCommand extends Commander
{
    public function handle()
    {
        $laravel = $this->laravel();

        $kernel = $laravel->make(ConsoleKernel::class);

        $input = new ArgvInput();
        $output = new ConsoleOutput();

        if (in_array($arg = $input->getFirstArgument(), ['logs', 'serve'])) {
            $input = new StringInput("isolaid:$arg");
        }

        if (blank($input->getFirstArgument()) && blank($input->getOptions()) && blank($input->getArguments())) {
            $input = new StringInput('list isolaid');
        }

        try {
            $status = $kernel->handle($input, $output);
        } catch (Throwable $error) {
            $status = $this->handleException($output, $error);
        }

        $kernel->terminate($input, $status);

        exit($status);
    }
}
