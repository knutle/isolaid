<?php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Knutle\Isolaid\Isolaid;
use Knutle\Isolaid\Tests\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

uses(TestCase::class)
    ->beforeAll(function () {
        Isolaid::bootstrap();

        ensureActiveIsolaidTestServer();
    })
    ->afterAll(function () {
        $process = getIsolaidTestServerProcess();

        if ($process->isRunning()) {
            $process->stop();
        }
    })
    ->in(__DIR__);

function callArtisanCommand(string $command, array $parameters = []): string
{
    $output = '';

    Event::listen(CommandFinished::class, function (CommandFinished $event) use (&$output) {
        $output = method_exists($event->output, 'fetch') ? $event->output->fetch() : '';
    });

    if (! in_array('--no-interaction', $parameters)) {
        $parameters['--no-interaction'] = true;
    }

    Artisan::call($command, $parameters);

    Event::forget(CommandFinished::class);

    return $output;
}

function isolaidGetRequest(string $route): Response
{
    ensureActiveIsolaidTestServer();

    return (new HttpClientFactory())->get('http://127.0.0.1:8010/'.ltrim($route, '/'));
}

function getIsolaidTestServerProcess(): Process
{
    static $process = new Symfony\Component\Process\Process(['testbench', 'isolaid:serve', '--no-interaction'], __DIR__.'/../');

    return $process;
}

function ensureActiveIsolaidTestServer(): Process
{
    $process = getIsolaidTestServerProcess();

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
