<?php

use Knutle\IsoView\IsoView;

it('can detect locking process when running serve command ', function () {
    IsoView::test()->ensureActiveTestServer();

    [ $status, $output ] = IsoView::test()->call('serve', ['--check-lock' => true]);

    expect(
        $status
    )->toBe(0)
     ->and(
         trim($output)
     )->toBeNumeric();

    [ $status, $output ] = IsoView::test()->call('serve', ['--fast-exit' => true]);

    expect($status)
        ->toBe(1)
        ->and(trim($output))
        ->toContain('Locked by: PID ')
        ->toContain('Listening interface locked by existing process: PID ')
        ->not()->toContain('Server running on [http://127.0.0.1:8010]')
        ->and(IsoView::test()->getServerProcess()->stop())
        ->toBe(143); // Termination (request to terminate)

    [ $status, $output ] = IsoView::test()->call('serve', ['--check-lock' => true]);

    expect($status)
        ->toBe(0)
        ->and(trim($output))
        ->toEqual('[OK] No locking process found');

    [ $status, $output ] = IsoView::test()->call('serve', ['--fast-exit' => true]);

    expect($status)
        ->toBe(143) // Termination (request to terminate)
        ->and(trim($output))
        ->toContain('Server running on [http://127.0.0.1:8010]')
        ->toContain('Fast exit after successful init')
        ->not->toContain('Locked by: PID ')
        ->not->toContain('Listening interface locked by existing process: PID ');

    [ $status, $output ] = IsoView::test()->call('serve', ['--check-lock' => true]);

    expect($status)
        ->toBe(0)
        ->and(trim($output))
        ->toEqual('[OK] No locking process found');
});
