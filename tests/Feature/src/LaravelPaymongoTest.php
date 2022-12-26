<?php

use Illuminate\Support\Facades\Http;
use function Pest\Faker\faker;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Request\RequestBodyData as SourceRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create\RequestBodyData as WebhookRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Update\RequestBodyData as UpdateWebhookRequestBody;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Create\ResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Disable\ResponseData as DisableWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Enable\ResponseData as EnableWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists\ResponseData as WebhookListResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Retrieve\ResponseData as RetrieveWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Update\ResponseData as UpdateWebhookResponseData;
use Twocngdagz\LaravelPaymongo\Enums\WebhookEventsEnum;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoMissingKeyException;
use Twocngdagz\LaravelPaymongo\Facades\LaravelPaymongo;

it('should throw paymongo missing key exception when api keys are not set', function () {
    config(['paymongo.public_key' => null]);
    config(['paymongo.secret_key' => null]);
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoMissingKeyException::class);

it('it should return a response source data after creating paymongo source from a valid request body', function () {
    config(['paymongo.public_key' => faker()->uuid]);
    config(['paymongo.secret_key' => faker()->uuid]);
    $uuid = faker()->uuid;
    $failedUrl = faker()->url;
    $successUrl = faker()->url;
    $baseUrl = faker()->url;
    $response = [
        'data' => [
            'id' => $uuid,
            'type' => 'source',
            'attributes' => [
                'amount' => 10000,
                'billing' => null,
                'currency' => 'PHP',
                'description' => null,
                'livemode' => false,
                'redirect' => [
                    'checkout_url' => $baseUrl.'/sources?id='.$uuid,
                    'failed' => $failedUrl,
                    'success' => $successUrl,
                ],
                'statement_descriptor' => null,
                'status' => 'pending',
                'type' => 'gcash',
                'metadata' => null,
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    Http::fake([
        '*' => Http::response($response, 200),
    ]);
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    $responseData = LaravelPaymongo::createSource($body);
    expect($responseData->data->attributes->redirect->success)->toBe($successUrl);
    expect($responseData->data->attributes->redirect->failed)->toBe($failedUrl);
    expect($responseData->data->id)->toBe($uuid);
});

it('should return a webhook response data after creating paymongo webhook from a valid request body', function () {
    config(['paymongo.public_key' => faker()->uuid]);
    config(['paymongo.secret_key' => faker()->uuid]);
    $url = faker()->url;
    $id = 'hook_'.faker()->uuid;
    $response = [
        'data' => [
            'id' => $id,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => faker()->uuid,
                'status' => 'enabled',
                'url' => 'http://test.com',
                'events' => [
                    'source.chargeable',
                ],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    Http::fake([
        '*' => Http::response($response, 200),
    ]);
    $body = WebhookRequestBodyData::from([
        'data' => [
            'attributes' => [
                'events' => [
                    WebhookEventsEnum::SOURCE_CHARGEABLE->value,
                ],
                'url' => faker()->url,
            ],
        ],
    ]);
    $response = LaravelPaymongo::createWebhook($body);
    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->data->id)->toBe($id);
    expect($response->data->type)->toBe('webhook');
});

it('should return a webhook response list data after retrieving all registered webhook from paymongo', function () {
    $id = 'hook_'.faker()->uuid;
    $secretKey = 'whsk_'.faker()->uuid;

    $response = [
        'data' => [
            [
                'id' => $id,
                'type' => 'webhook',
                'attributes' => [
                    'livemode' => false,
                    'secret_key' => $secretKey,
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
        '*' => Http::response($response, 200),
    ]);
    $response = LaravelPaymongo::listWebhooks();

    expect($response->data->first()->id)->toBe($id);
    expect($response)->toBeInstanceOf(WebhookListResponseData::class);
    expect($response->data)->toHaveCount(2);
    expect($response->data->first()->attributes->secret_key)->toBe($secretKey);
});

it('should return webhook response get data after retrieving a webhook from paymongo', function () {
    $id = 'hook_'.faker()->uuid;
    $secretKey = 'whsk_'.faker()->uuid;
    $response = [
        'data' => [
            'id' => $id,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => $secretKey,
                'status' => 'enabled',
                'url' => faker()->url,
                'events' => [
                    'payment.paid',
                ],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    Http::fake([
        '*' => Http::response($response, 200),
    ]);
    $response = LaravelPaymongo::retrieveWebhook('hook_j9WUB2sbQ8h9xJCn37wb4pb8');
    expect($response)->toBeInstanceOf(RetrieveWebhookResponseData::class);
    expect($response->data->id)->toBe($id);
});

it('should return webhook response disable data after disabling a webhook from paymongo', function () {
    $id = 'hook_'.faker()->uuid;
    $secretKey = 'whsk_'.faker()->uuid;
    $response = [
        'data' => [
            'id' => $id,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => $secretKey,
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
    Http::fake([
        '*' => Http::response($response, 200),
    ]);
    $response = LaravelPaymongo::disableWebhook('hook_j9WUB2sbQ8h9xJCn37wb4pb8');
    expect($response)->toBeInstanceOf(DisableWebhookResponseData::class);
    expect($response->data->attributes->status)->toBe('disabled');
});

it('should return webhook response enable data after enabling a webhook from paymongo', function () {
    $id = 'hook_'.faker()->uuid;
    $secretKey = 'whsk_'.faker()->uuid;
    $response = [
        'data' => [
            'id' => $id,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => $secretKey,
                'status' => 'enabled',
                'url' => faker()->url,
                'events' => [
                    'payment.paid',
                ],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    Http::fake([
        '*' => Http::response($response, 200),
    ]);

    $response = LaravelPaymongo::enableWebhook('hook_j9WUB2sbQ8h9xJCn37wb4pb8');
    expect($response)->toBeInstanceOf(EnableWebhookResponseData::class);
    expect($response->data->attributes->status)->toBe('enabled');
});

it('should return wewbhook response update data after updating a webhook from paymongo', function () {
    $id = 'hook_'.faker()->uuid;
    $secretKey = 'whsk_'.faker()->uuid;
    $requestBody = UpdateWebhookRequestBody::from([
        'data' => [
            'attributes' => [
                'events' => [
                    WebhookEventsEnum::SOURCE_CHARGEABLE->value,
                ],
            ],
        ],
    ]);
    $response = [
        'data' => [
            'id' => $id,
            'type' => 'webhook',
            'attributes' => [
                'livemode' => false,
                'secret_key' => $secretKey,
                'status' => 'disabled',
                'url' => faker()->url,
                'events' => [
                    WebhookEventsEnum::SOURCE_CHARGEABLE->value,
                ],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ],
        ],
    ];
    Http::fake([
        '*' => Http::response($response, 200),
    ]);
    $response = LaravelPaymongo::updateWebhook($requestBody, $id);
    expect($response)->toBeInstanceOf(UpdateWebhookResponseData::class);
    expect($response->data->attributes->events)->toContain(WebhookEventsEnum::SOURCE_CHARGEABLE->value);
});
