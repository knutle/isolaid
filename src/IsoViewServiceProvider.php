<?php

namespace Knutle\IsoView;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class IsoViewServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package
            ->name('isoview')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes('core');
    }

    public function packageBooted()
    {
        $this->publishes([
            $this->package->basePath('/../routes/user.php') => IsoView::getRootPackagePath('routes/isoview.php'),
        ], "{$this->package->shortName()}-routes");
    }
}
