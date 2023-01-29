<?php

namespace Twocngdagz\LaravelPaymongo\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\WebhookClient\WebhookClientServiceProvider;
use Twocngdagz\LaravelPaymongo\LaravelPaymongoServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Twocngdagz\\LaravelPaymongo\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            WebhookClientServiceProvider::class,
            LaravelPaymongoServiceProvider::class,
            LaravelDataServiceProvider::class,

        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('webhook-client.configs.0.process_webhook_job', \Twocngdagz\LaravelPaymongo\Jobs\ProcessWebhookFromPaymongo::class);
        config()->set('webhook-client.configs.0.signature_validator', \Twocngdagz\LaravelPaymongo\SignatureValidator\PaymongoSignatureValidator::class);
        config()->set('webhook-client.configs.0.signature_header_name', 'Paymongo-Signature');

        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_laravel-paymongo_table.php';
        $migration->up();
        $webclient_migration = include __DIR__.'/../vendor/spatie/laravel-webhook-client/database/migrations/create_webhook_calls_table.php.stub';
        $webclient_migration->up();
    }
}
