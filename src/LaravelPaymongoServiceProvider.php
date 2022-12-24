<?php

namespace Twocngdagz\LaravelPaymongo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Twocngdagz\LaravelPaymongo\Commands\LaravelPaymongoCommand;

class LaravelPaymongoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-paymongo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-paymongo_table')
            ->hasCommand(LaravelPaymongoCommand::class);
    }
}
