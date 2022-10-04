<?php

use Knutle\IsoView\IsoView;
use function Pest\Laravel\artisan;

it('can run install command', function () {
    $publishedRoutesFile = IsoView::getRootPackagePath('routes/isoview.php');

    expect($publishedRoutesFile)->not->toBeReadableFile();

    artisan('isoview:install')
        ->expectsOutputToContain('Publishing routes...')
        ->expectsOutputToContain('isoview has been installed!')
        ->assertSuccessful();

    expect($publishedRoutesFile)->toBeReadableFile()
                                ->and(file_get_contents($publishedRoutesFile))
                                ->toEqual(file_get_contents(IsoView::getRootPackagePath('routes/user.php')));

    unlink($publishedRoutesFile);
});

it('can detect locking process when running serve command then exit lock owner and retry command ', function () {
    expect(
        trim(callArtisanCommand('isoview:serve', ['--check-lock' => true]))
    )->toBeNumeric();

    artisan('isoview:serve', ['--fast-exit' => true])
        ->expectsOutputToContain('Locked by: PID ')
        ->expectsOutputToContain('Listening interface locked by existing process: PID ')
        ->doesntExpectOutputToContain('Server running on [http://127.0.0.1:8010]')
        ->assertFailed();

    getIsoViewTestServerProcess()->stop();

    expect(
        trim(callArtisanCommand('isoview:serve', ['--check-lock' => true]))
    )->toEqual('[OK] No locking process found');

    artisan('isoview:serve', ['--fast-exit' => true])
        ->doesntExpectOutputToContain('Locked by: PID ')
        ->doesntExpectOutputToContain('Listening interface locked by existing process: PID ')
        ->expectsOutputToContain('Server running on [http://127.0.0.1:8010]')
        ->expectsOutputToContain('Fast exit after successful init')
        ->assertExitCode(143); // Termination (request to terminate)

    expect(
        trim(callArtisanCommand('isoview:serve', ['--check-lock' => true]))
    )->toEqual('[OK] No locking process found');

    ensureActiveIsoViewTestServer();
});
