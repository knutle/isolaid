<?php

use Illuminate\Http\Client\ConnectionException;
use Knutle\IsoView\IsoView;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertEquals;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

it('can stop and start server', function () {
    expect(IsoView::test()->get('/')->status())
        ->toEqual(200);

    $process = IsoView::test()->getServerProcess();
    $process->stop();

    expect(fn () => IsoView::test()->get('/')->status())
        ->toThrow(ConnectionException::class);

    IsoView::test()->ensureActiveTestServer();

    expect(IsoView::test()->get('/')->status())
        ->toEqual(200);
});

it('can list all available routes', function () {
    assertMatchesJsonSnapshot(
        get('/routes.json')->content()
    );
});

it('can run server and visit all links from index', function (string $route) {
    $internalContent = get($route)->content();
    $externalContent = IsoView::test()->get($route)->body();

    assertEquals($internalContent, $externalContent);

    assertMatchesHtmlSnapshot(
        $externalContent
    );
})->with([
    'index' => '/',
    'default' => '/default',
]);
