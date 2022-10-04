<?php

namespace Knutle\Isolaid;

use Composer\InstalledVersions;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Concerns\HandlesRoutes;
use Symfony\Component\Filesystem\Path;

class Isolaid
{
    use CreatesApplication;
    use HandlesRoutes;

    public ?Application $app = null;

    public bool $enablesPackageDiscoveries = true;

    public function __construct()
    {
        if (is_null($this->app)) {
            $this->refreshApplication();
        }
    }

    public static function bootstrap(): Isolaid
    {
        $instance = (new static());

        $instance->setUpApplicationRoutes();

        return $instance;
    }

    protected function refreshApplication(): void
    {
        $this->app = $this->createApplication();
    }

    protected function getPackageProviders($app): array
    {
        return [
            IsolaidServiceProvider::class,
        ];
    }

    protected function defineEnvironment(Application $app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.debug', true);
    }

    public static function getRootPackagePath(string $path = null): string
    {
        return Path::join(
            InstalledVersions::getRootPackage()['install_path'],
            $path
        );
    }
}
