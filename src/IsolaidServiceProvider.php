<?php

namespace Knutle\Isolaid;

use Knutle\Isolaid\Commands\IsolaidLogsCommand;
use Knutle\Isolaid\Commands\IsolaidServeCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class IsolaidServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */

        $package
            ->name('isolaid')
            ->hasConfigFile()
            ->hasCommand(IsolaidLogsCommand::class)
            ->hasCommand(IsolaidServeCommand::class)
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
            $this->package->basePath('/../routes/user.php') => Isolaid::getRootPackagePath('routes/isolaid.php'),
        ], "{$this->package->shortName()}-routes");
    }
}
