<?php

namespace Knutle\IsoView;

use Knutle\IsoView\Commands\IsoViewLogsCommand;
use Knutle\IsoView\Commands\IsoViewServeCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasCommand(IsoViewLogsCommand::class)
            ->hasCommand(IsoViewServeCommand::class)
            ->hasViews()
            ->hasInstallCommand(
                fn (InstallCommand $installCommand) => $installCommand->startWith(
                    function (InstallCommand $installCommand) {
                        $installCommand->comment('Publishing routes...');

                        $installCommand->callSilently('vendor:publish', [
                            '--tag' => "{$this->package->shortName()}-routes",
                        ]);
                    }
                )
            )
            ->hasRoutes('core');
    }

    public function packageBooted()
    {
        $this->publishes([
            $this->package->basePath('/../routes/user.php') => IsoView::getRootPackagePath('routes/isoview.php'),
        ], "{$this->package->shortName()}-routes");
    }
}
