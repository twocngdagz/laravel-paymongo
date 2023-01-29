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
            ->hasConfigFile([
                'paymongo',
                'webhook-client',
            ])
            /*
            ->hasViews()
            */
            ->hasMigration('create_laravel-paymongo_table')
            ->hasRoute('web')
            ->hasCommand(LaravelPaymongoCommand::class);
    }
}
