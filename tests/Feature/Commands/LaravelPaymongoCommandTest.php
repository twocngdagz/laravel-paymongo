<?php

use Illuminate\Support\Facades\Http;
use function Pest\Faker\faker;
use Symfony\Component\Console\Command\Command;
use Twocngdagz\LaravelPaymongo\Commands\LaravelPaymongoCommand;

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
    $this->artisan(LaravelPaymongoCommand::class)
        ->expectsOutput('We found 2 webhook that is registered!')
        ->expectsTable([
            'ID',
            'URL',
        ], [
            [$webhookResponseList['data'][0]['id'], $webhookResponseList['data'][0]['attributes']['url']],
            [$webhookResponseList['data'][1]['id'], $webhookResponseList['data'][1]['attributes']['url']],
        ])
        ->expectsConfirmation('Are you sure you want to continue in deleting this registered webhook?', 'Yes')
        ->assertExitCode(Command::SUCCESS);
});
