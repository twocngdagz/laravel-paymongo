<?php

use Illuminate\Support\Facades\Http;
use function Pest\Faker\faker;
use Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Request\RequestBodyData;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoMissingKeyException;
use Twocngdagz\LaravelPaymongo\Facades\LaravelPaymongo;

it('should_throw_paymongo_missing_key_exception_when_api_keys_are_not_set', function () {
    config(['paymongo.public_key' => null]);
    config(['paymongo.secret_key' => null]);
    $body = new RequestBodyData([
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

it('it_should_create_a_source_resource_on_a_successful_response', function () {
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
    $body = new RequestBodyData([
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
