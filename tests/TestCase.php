<?php

namespace Knutle\Isolaid\Tests;

use Knutle\Isolaid\IsolaidServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IsolaidServiceProvider::class,
        ];
    }
}
