<?php

namespace Twocngdagz\LaravelPaymongo\Commands;

use Illuminate\Console\Command;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists\Data;
use Twocngdagz\LaravelPaymongo\Enums\WebhookEventsEnum;
use Twocngdagz\LaravelPaymongo\Facades\LaravelPaymongo;
use Twocngdagz\LaravelPaymongo\Jobs\Webhooks\DisableWebhook;

class LaravelPaymongoCommand extends Command
{
    public $signature = 'laravel-paymongo';

    public $description = 'This will register the webhooks in paymongo and disable all previous registered webhooks.';

    public function handle(): int
    {
        $data = $this->retrieveWebhooksSourceChargeablePaymentPaidPaymentFailedEvents();
        $webhookData = Data::collection($data);

        if ($webhookData->count()) {
            $this->line('We found '.$webhookData->count().' webhook that is registered!');
            $webhooks = collect($webhookData->toArray())->map(function ($webhook) {
                return [$webhook['id'], $webhook['attributes']['url']];
            });
            $this->table(
                ['ID', 'URL'],
                $webhooks
            );
            $continue = $this->confirm('Are you sure you want to continue in disabling this registered webhook?');
            if (! $continue) {
                return self::SUCCESS;
            }
            $webhookData->each(function ($webhook) {
                DisableWebhook::dispatch($webhook->id);
            });
        }

        $this->comment('All done');

        return self::SUCCESS;
    }

    public function retrieveWebhooksSourceChargeablePaymentPaidPaymentFailedEvents()
    {
        $response = LaravelPaymongo::listWebhooks();

        return array_filter($response->data->toArray(), function ($webhook) {
            return in_array($webhook['attributes']['events'][0], array_column(WebhookEventsEnum::cases(), 'value'));
        });
    }
}
