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
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoBadRequestException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoMissingKeyException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoUnauthorizedException;
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
                        WebhookEventsEnum::PAYMENT_PAID->value,
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
                        WebhookEventsEnum::SOURCE_CHARGEABLE->value,
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
                    WebhookEventsEnum::PAYMENT_PAID->value,
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
                    WebhookEventsEnum::PAYMENT_PAID->value,
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
    expect($response)->toBeInstanceOf(DisableWebhookResponseData::class)
        ->and($response->data->attributes->status)->toBe('disabled');
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
                    WebhookEventsEnum::PAYMENT_PAID->value,
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

it('it should throw a bad request exception if request is using unsupported currency when creating a source', function () {
    $response = [
        'errors' => [
            [
                'code' => 'parameter_invalid',
                'detail' => 'PHP is the only currency supported at the moment.',
                'source' => [
                    'pointer' => 'currency',
                    'attribute' => 'currency',
                ],
            ],

        ],
    ];
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'USD',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 400),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoBadRequestException::class, 'PHP is the only currency supported at the moment.');

it('should throw an exception if request is using unsupported type of source when creating a source', function () {
    $response = [
        'errors' => [
            [
                'code' => 'parameter_invalid',
                'detail' => 'The source_type passed foodpanda is invalid.',
                'source' => [
                    'pointer' => 'source_type',
                    'attribute' => 'source_type',
                ],
            ],

        ],
    ];
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'foodpanda',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 400),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoBadRequestException::class, 'The source_type passed foodpanda is invalid.');

it('should throw an exception if amount is below minimum when creating a source', function () {
    $response = [
        'errors' => [
            [
                'code' => 'parameter_invalid',
                'detail' => 'The value for amount cannot be less than 10000.',
                'source' => [
                    'pointer' => 'amount',
                    'attribute' => 'amount',
                ],
            ],

        ],
    ];
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 0,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 400),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoBadRequestException::class, 'The value for amount cannot be less than 10000.');

it('should throw an exception if the value of redirect success is blank when creating a source', function () {
    $response = [
        'errors' => [
            [
                'code' => 'parameter_blank',
                'detail' => 'The value for redirect.success cannot be blank.',
                'source' => [
                    'pointer' => 'redirect.success',
                    'attribute' => 'success',
                ],
            ],

        ],
    ];
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => '',
                    'failed' => 'http://demo-store.test/hub',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 400),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoBadRequestException::class, 'The value for redirect.success cannot be blank.');

it('shouild throw an exception if the value of redirect fail is blank when creating a source', function () {
    $response = [
        'errors' => [
            [
                'code' => 'parameter_blank',
                'detail' => 'The value for redirect.failed cannot be blank.',
                'source' => [
                    'pointer' => 'redirect.failed',
                    'attribute' => 'failed',
                ],
            ],

        ],
    ];
    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => '',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 400),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoBadRequestException::class, 'The value for redirect.failed cannot be blank.');

it('should throw an exception when using invalid keys when creating a source', function () {
    config(['paymongo.public_key' => 'invalid_key']);
    config(['paymongo.secret_key' => 'invalid_key']);

    $response = [
        'errors' => [
            [
                'code' => 'api_key_invalid',
                'detail' => 'API key invalid_key is invalid. Go to https://developers.paymongo.com/docs/authentication to know more about our API authentication.',
            ],

        ],
    ];

    $body = SourceRequestBodyData::from([
        'data' => [
            'attributes' => [
                'amount' => 10000,
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => 'http://demo-store.test/hub',
                    'failed' => '',
                ],
            ],
        ],
    ]);
    Http::fake([
        '*' => Http::response($response, 401),
    ]);
    LaravelPaymongo::createSource($body);
})->throws(PaymongoUnauthorizedException::class, 'API key invalid_key is invalid.');

it('it should throws an exception when using public key on url that required secret key', function () {
    $publicKey = config('paymongo.public_key');
    config(['paymongo.secret_key' => $publicKey]);
    $response = [
        'errors' => [
            [
                'code' => 'secret_key_required',
                'detail' => 'Please use your secret key to access this resource. Go to https://developers.paymongo.com/docs/authentication to know more about our API authentication.',
            ],

        ],
    ];
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
    Http::fake([
        '*' => Http::response($response, 401),
    ]);
    $response = LaravelPaymongo::createWebhook($body);
})->throws(PaymongoUnauthorizedException::class, 'Please use your secret key to access this resource.');
