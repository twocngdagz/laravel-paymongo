<?php

namespace Twocngdagz\LaravelPaymongo;

use Illuminate\Support\Facades\Http;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Request\RequestBodyData as SourceRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Response\ResponseData as SourceResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create\RequestBodyData as WebhookRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Create\ResponseData as WebhookResponseData;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoMissingKeyException;

class LaravelPaymongo
{
    protected string $paymongoUrl = 'https://api.paymongo.com/v1/';

    protected ?string $publicKey = null;

    protected ?string $secretKey = null;

    public function init()
    {
        $this->publicKey = config('paymongo.public_key');
        $this->secretKey = config('paymongo.secret_key');

        if ($this->publicKey === null || $this->secretKey === null) {
            throw new PaymongoMissingKeyException;
        }
    }

    public function createSource(SourceRequestBodyData $body): SourceResponseData
    {
        $path = 'sources';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])
        ->withBasicAuth($this->publicKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return SourceResponseData::from($response->json());
    }

    public function createWebhook(WebhookRequestBodyData $body): WebhookResponseData
    {
        $path = 'webhooks';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
        ->withBasicAuth($this->secretKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return WebhookResponseData::from($response->json());
    }
}
