<?php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Knutle\IsoView\IsoView;
use Knutle\IsoView\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

uses()
    ->beforeAll(function () {
        IsoView::test()->ensureActiveTestServer();
    })
    ->afterAll(function () {
        $process = IsoView::test()->getServerProcess();

        if ($process->isRunning()) {
            $process->stop();
        }
    })
    ->in(__DIR__.'/Server');

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

expect()->extend('toMatchSnapshot', function () {
    test()->assertMatchesSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchFileHashSnapshot', function () {
    test()->assertMatchesFileHashSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchFileSnapshot', function () {
    test()->assertMatchesFileSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchHtmlSnapshot', function () {
    test()->assertMatchesHtmlSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchJsonSnapshot', function () {
    test()->assertMatchesJsonSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchObjectSnapshot', function () {
    test()->assertMatchesObjectSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchTextSnapshot', function () {
    test()->assertMatchesTextSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchXmlSnapshot', function () {
    test()->assertMatchesXmlSnapshot($this->value);

    return $this;
});

expect()->extend('toMatchYamlSnapshot', function () {
    test()->assertMatchesYamlSnapshot($this->value);

    return $this;
});
