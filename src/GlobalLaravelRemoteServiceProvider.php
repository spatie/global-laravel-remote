<?php

namespace Spatie\GlobalLaravelRemote;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\GlobalLaravelRemote\Commands\GlobalLaravelRemoteCommand;

class GlobalLaravelRemoteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('global-laravel-remote')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_global-laravel-remote_table')
            ->hasCommand(GlobalLaravelRemoteCommand::class);
    }
}
