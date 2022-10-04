<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;

it('can run server and visit all links from index', function (string $route) {
    assertMatchesHtmlSnapshot(
        Http::get('http://127.0.0.1:8010'.$route)->body()
    );
})->with(
    fn () => isolaidGetRequest('/routes.json')->collect()->pluck('uri', 'name')
);

it('can stop and start server', function () {
    expect(Http::get('http://127.0.0.1:8010')->status())
        ->toEqual(200);

    $process = getIsolaidTestServerProcess();
    $process->stop();

    expect(fn () => Http::get('http://127.0.0.1:8010')->status())
        ->toThrow(ConnectionException::class);

    ensureActiveIsolaidTestServer();

    expect(Http::get('http://127.0.0.1:8010')->status())
        ->toEqual(200);
});
