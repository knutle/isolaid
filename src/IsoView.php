<?php

namespace Knutle\IsoView;

use function array_merge;
use Composer\InstalledVersions;
use function file_exists;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Foundation\Console\VendorPublishCommand;
use function is_null;
use Knutle\IsoView\Console\Commands\InstallCommand;
use Knutle\IsoView\Console\Commands\LogsCommand;
use Knutle\IsoView\Console\Commands\ServeCommand;
use Knutle\IsoView\Testing\IsoViewTestingHelper;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Concerns\HandlesRoutes;
use Symfony\Component\Filesystem\Path;

final class IsoView
{
    use CreatesApplication;
    use HandlesRoutes;

    public static ?IsoViewTestingHelper $testHelper = null;

    protected static ?IsoView $instance = null;

    protected ?Application $app = null;

    public bool $enablesPackageDiscoveries = true;

    public static function test(): IsoViewTestingHelper
    {
        return IsoView::$testHelper ?? IsoView::$testHelper = new IsoViewTestingHelper();
    }

    protected static function instance(): IsoView
    {
        return IsoView::$instance ?? IsoView::$instance = new IsoView();
    }

    public static function app(): Application
    {
        if (is_null(IsoView::instance()->app)) {
            IsoView::instance()->refreshApplication();
        }

        return IsoView::instance()->app;
    }

    protected function refreshApplication(): void
    {
        $this->app = $this->createApplication();
    }

    protected function getPackageProviders($app): array
    {
        return $this->providers($app);
    }

    protected function defineEnvironment(Application $app): void
    {
        $this->config($app)->set('app.debug', true);
    }

    protected static function config(Application $app = null): Repository
    {
        return $app['config'] ?? IsoView::app()['config'];
    }

    public static function providers(Application $app = null): array
    {
        $fallback = [];

        if (file_exists($fallbackPath = IsoView::getRootPackagePath('config/isoview.php'))) {
            $fallback = data_get(include $fallbackPath, 'providers');
        }

        return array_merge([
            IsoViewServiceProvider::class,
        ], IsoView::config($app)->get('isoview.providers', $fallback));
    }

    public static function commands(): array
    {
        return [
            ServeCommand::class,
            LogsCommand::class,
            InstallCommand::class,
            VendorPublishCommand::class,
            RouteListCommand::class,
        ];
    }

    public static function getRootPackagePath(string $path = null): string
    {
        return Path::join(
            InstalledVersions::getRootPackage()['install_path'],
            $path ?? ''
        );
    }
}
