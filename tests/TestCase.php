<?php

namespace Knutle\IsoView\Tests;

use Knutle\IsoView\IsoViewServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IsoViewServiceProvider::class,
        ];
    }
}
