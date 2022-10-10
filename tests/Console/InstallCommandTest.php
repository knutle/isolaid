<?php

use Knutle\IsoView\IsoView;

it('can run install command', function () {
    $publishedRoutesFile = IsoView::getRootPackagePath('routes/isoview.php');

    expect(
        file_exists($publishedRoutesFile)
    )->toBeFalse();

    [ $exitCode, $output ] = IsoView::test()->call('install');

    expect($output)
        ->toContain(
            'Publishing routes...',
            '[OK] Install successful!',
            'Run ./vendor/bin/isoview to list available commands.'
        )
        ->and($exitCode)
        ->toBe(0)
        ->and($publishedRoutesFile)
        ->toBeReadableFile()
        ->and(file_get_contents($publishedRoutesFile))
        ->toEqual(file_get_contents(IsoView::getRootPackagePath('routes/user.php')));

    unlink($publishedRoutesFile);
});
