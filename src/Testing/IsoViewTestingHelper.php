<?php

namespace Knutle\IsoView\Testing;

use function collect;
use function func_get_args;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\Response;
use function is_bool;
use JetBrains\PhpStorm\ArrayShape;
use Knutle\IsoView\Console\BinaryHandler;
use Knutle\IsoView\IsoView;
use function ltrim;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class IsoViewTestingHelper
{
    protected static ?Process $server = null;

    public function get(string $route): Response
    {
        return (new HttpClientFactory())->get('http://127.0.0.1:8010/'.ltrim($route, '/'));
    }

    #[ArrayShape([0 => 'int', 1 => 'string'])]
    public function call(string $command, array $parameters = []): array
    {
        return [
            BinaryHandler::run(
                new ArrayInput(['command' => $command, ...$parameters]),
                $output = new BufferedOutput()
            ),
            $output->fetch(),
        ];
    }

    protected function zipInput(array $parameters = []): array
    {
        return collect($parameters)->keys()->zip(
            collect($parameters)->values()->map(fn (mixed $value) => is_bool($value) ? null : $value)
        )->collapse()->whereNotNull()->values()->toArray();
    }

    public function process(string $command, array $parameters = []): Process
    {
        return new Process(['./bin/isoview', $command, ...$this->zipInput($parameters)]);
//        return new Process(['./vendor/bin/testbench', $command, ...$zippedParameters]);
    }

    public function spawn(string $command, array $parameters = []): Process
    {
        $process = $this->process(...func_get_args());

        $process->start();

        return $process;
    }

    public function getServerProcess(): Process
    {
        return static::$server ?? static::$server = IsoView::test()->process('serve', ['--no-interaction' => true]);
    }

    public function ensureActiveTestServer(): Process
    {
        $process = IsoView::test()->getServerProcess();

        if ($process->isRunning()) {
            return $process;
        }

        $process->start();

        $ready = $process->waitUntil(
            fn ($type, $output) => str_contains($output, 'Server running on [http://127.0.0.1:8010]')
        );

        if (! $ready) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }
}
