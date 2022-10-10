<?php

namespace Knutle\IsoView\Tests;

use Knutle\IsoView\IsoViewServiceProvider;
use Knutle\TestStubs\InteractsWithStubs;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithStubs;

    protected function getPackageProviders($app): array
    {
        return [
            IsoViewServiceProvider::class,
        ];
    }
}
