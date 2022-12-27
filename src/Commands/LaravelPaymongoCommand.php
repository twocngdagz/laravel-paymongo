<?php

namespace Twocngdagz\LaravelPaymongo\Commands;

use Illuminate\Console\Command;
use Twocngdagz\LaravelPaymongo\Facades\LaravelPaymongo;
use Twocngdagz\LaravelPaymongo\Jobs\Webhooks\DisableWebhook;

class LaravelPaymongoCommand extends Command
{
    public $signature = 'laravel-paymongo';

    public $description = 'This will register the webhooks in paymongo and delete all previous registered webhooks.';

    public function handle(): int
    {
        $response = LaravelPaymongo::listWebhooks();

        if ($response->data->count()) {
            $this->line('We found '.$response->data->count().' webhook that is registered!');
            $webhooks = collect($response->data->toArray())->map(function ($webhook) {
                return [$webhook['id'], $webhook['attributes']['url']];
            });
            $this->table(
                ['ID', 'URL'],
                $webhooks
            );
            $continue = $this->confirm('Are you sure you want to continue in deleting this registered webhook?');
            if (! $continue) {
                return self::SUCCESS;
            }
            $response->data->each(function ($webhook) {
                DisableWebhook::dispatch($webhook->id);
            });
        }

        $this->comment('All done');

        return self::SUCCESS;
    }
}
