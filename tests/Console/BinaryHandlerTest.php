<?php

use Illuminate\Support\Str;
use Knutle\IsoView\IsoView;

it('can run binary', function () {
    $process = IsoView::test()->spawn('');

    expect($process->wait())
        ->toBe(0)
        ->and((string) Str::of($process->getOutput())->after("\n\n"))
        ->toMatchTextSnapshot();
});

it('can list available command by default', function () {
    [ $status, $output ] = IsoView::test()->call('');

    expect($status)
        ->toBe(0)
        ->and((string) Str::of($output)->afterLast('Available commands:')->prepend('Available commands:'))
        ->toMatchTextSnapshot();
});

it('binary can pass parameters through to command correctly', function () {
    $process = IsoView::test()->spawn('serve', ['--check-lock' => true]);

    $process->wait();

    expect(trim($process->getOutput()))
        ->toBeNumeric();
});
