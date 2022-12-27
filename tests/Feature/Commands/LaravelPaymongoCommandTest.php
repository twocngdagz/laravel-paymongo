<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use function Pest\Faker\faker;
use Symfony\Component\Console\Command\Command;
use Twocngdagz\LaravelPaymongo\Commands\LaravelPaymongoCommand;
use Twocngdagz\LaravelPaymongo\Enums\WebhookEventsEnum;
use Twocngdagz\LaravelPaymongo\Jobs\Webhooks\DisableWebhook;

it('should_display_correct_output', function () {
    Bus::fake();

    $webhookResponseDisable = [
        'data' => [
            'id' => 'hook_'.faker()->uuid,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => 'whsk_'.faker()->uuid,
                'status' => 'disabled',
                'url' => faker()->url,
                'events' => [
                    'payment.paid',
                ],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    $webhookResponseList = [
        'data' => [
            [
                'id' => 'hook_'.faker()->uuid,
                'type' => 'webhook',
                'attributes' => [
                    'livemode' => false,
                    'secret_key' => 'whsk_'.faker()->uuid,
                    'status' => 'enabled',
                    'url' => faker()->url,
                    'events' => [
                        'does.not.exists',
                    ],
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ],
            ],
            [
                'id' => 'hook_'.faker()->uuid,
                'type' => 'webhook',
                'attributes' => [
                    'livemode' => false,
                    'secret_key' => 'whsk_'.faker()->uuid,
                    'status' => 'enabled',
                    'url' => faker()->url,
                    'events' => [
                        'payment.paid',
                    ],
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ],
            ],
            [
                'id' => 'hook_'.faker()->uuid,
                'type' => 'webhook',
                'attributes' => [
                    'livemode' => false,
                    'secret_key' => faker()->uuid,
                    'status' => 'enabled',
                    'url' => faker()->url,
                    'events' => [
                        'source.chargeable',
                    ],
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ],
            ],
        ],
    ];
    Http::fake([
        'https://api.paymongo.com/v1/webhooks' => Http::response($webhookResponseList, 200),
        '*' => Http::response($webhookResponseDisable, 200),
        '*' => Http::response($webhookResponseDisable, 200),
    ]);

    $rows = [];

    foreach ($webhookResponseList['data'] as $webhook) {
        if (in_array($webhook['attributes']['events'][0], array_column(WebhookEventsEnum::cases(), 'value'))) {
            $rows[] = [$webhook['id'], $webhook['attributes']['url']];
        }
    }
    $count = count($rows);
    $this->artisan(LaravelPaymongoCommand::class)
        ->expectsOutput("We found {$count} webhook that is registered!")
        ->expectsTable([
            'ID',
            'URL',
        ], $rows)
        ->expectsConfirmation('Are you sure you want to continue in disabling this registered webhook?', 'Yes')
        ->assertExitCode(Command::SUCCESS);
    Bus::assertDispatched(DisableWebhook::class, $count);
});
