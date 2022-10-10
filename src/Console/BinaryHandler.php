<?php

namespace Knutle\IsoView\Console;

use Closure;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Knutle\IsoView\IsoView;
use Orchestra\Testbench\Console\Commander;
use Orchestra\Testbench\Console\Kernel;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class BinaryHandler extends Commander
{
    protected InputInterface $input;

    protected OutputStyle $output;

    public static function run(InputInterface $input = new ArgvInput(), OutputInterface $output = new ConsoleOutput())
    {
        $config = [
            'env' => ['APP_KEY="AckfSECXIvnK5r28GVIWUAxmbBSjTsmF"', 'DB_CONNECTION="testing"', 'APP_DEBUG=true'],
            'providers' => IsoView::providers(),
            'dont-discover' => [],
        ];

        $instance = new static($config, IsoView::getRootPackagePath());

        $instance->input = $input;
        $instance->output = new OutputStyle($instance->input, $output);

        return $instance->handle();
    }

    public function handle()
    {
        $laravel = $this->laravel();

        /** @var Kernel $kernel */
        $kernel = $laravel->make(ConsoleKernel::class);

        Artisan::forgetBootstrappers();

        Artisan::starting(function (Artisan $application) {
            $application->resolveCommands(
                IsoView::commands()
            );
        });

        try {
            $status = $kernel->handle($this->input, $this->output);
        } catch (Throwable $error) {
            $status = $this->handleException($this->output, $error);
        }

        $kernel->terminate($this->input, $status);

        return $status;
    }

    protected function resolveApplicationCallback(): Closure
    {
        return function () {
            $this->createDotenv()->load();
        };
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', 'Orchestra\Testbench\Console\Kernel');
    }
}
