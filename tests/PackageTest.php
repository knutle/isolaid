<?php

use Knutle\Isolaid\Isolaid;
use function Pest\Laravel\artisan;

it('can run install command', function () {
    $publishedRoutesFile = Isolaid::getRootPackagePath('routes/isolaid.php');

    expect($publishedRoutesFile)->not->toBeReadableFile();

    artisan('isolaid:install')
        ->expectsOutputToContain('Publishing routes...')
        ->expectsOutputToContain('isolaid has been installed!')
        ->assertSuccessful();

    expect($publishedRoutesFile)->toBeReadableFile()
                                ->and(file_get_contents($publishedRoutesFile))
                                ->toEqual(file_get_contents(Isolaid::getRootPackagePath('routes/user.php')));

    unlink($publishedRoutesFile);
});

it('can detect locking process when running serve command then exit lock owner and retry command ', function () {
    expect(
        trim(callArtisanCommand('isolaid:serve', ['--check-lock' => true]))
    )->toBeNumeric();

    artisan('isolaid:serve', ['--fast-exit' => true])
        ->expectsOutputToContain('Locked by: PID ')
        ->expectsOutputToContain('Listening interface locked by existing process: PID ')
        ->doesntExpectOutputToContain('Server running on [http://127.0.0.1:8010]')
        ->assertFailed();

    getIsolaidTestServerProcess()->stop();

    expect(
        trim(callArtisanCommand('isolaid:serve', ['--check-lock' => true]))
    )->toEqual('[OK] No locking process found');

    artisan('isolaid:serve', ['--fast-exit' => true])
        ->doesntExpectOutputToContain('Locked by: PID ')
        ->doesntExpectOutputToContain('Listening interface locked by existing process: PID ')
        ->expectsOutputToContain('Server running on [http://127.0.0.1:8010]')
        ->expectsOutputToContain('Fast exit after successful init')
        ->assertExitCode(143); // Termination (request to terminate)

    expect(
        trim(callArtisanCommand('isolaid:serve', ['--check-lock' => true]))
    )->toEqual('[OK] No locking process found');

    ensureActiveIsolaidTestServer();
});
