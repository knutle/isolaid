<?php

use function Pest\Laravel\get;
use function Spatie\Snapshots\assertMatchesJsonSnapshot;

it('can list all available routes', function () {
    assertMatchesJsonSnapshot(
        get('/routes.json')->content()
    );
});
