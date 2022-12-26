<?php

namespace Twocngdagz\LaravelPaymongo;

use Illuminate\Support\Facades\Http;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Request\RequestBodyData as CreateSourceRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Response\ResponseData as CreateSourceResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create\RequestBodyData as CreateWebhookRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Create\ResponseData as CreateWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Disable\ResponseData as DisableWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists\ResponseData as ListWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Retrieve\ResponseData as RetrieveWebhookResponseData;
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

    public function createSource(CreateSourceRequestBodyData $body): CreateSourceResponseData
    {
        $path = 'sources';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])
        ->withBasicAuth($this->publicKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return CreateSourceResponseData::from($response->json());
    }

    public function createWebhook(CreateWebhookRequestBodyData $body): CreateWebhookResponseData
    {
        $path = 'webhooks';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
        ->withBasicAuth($this->secretKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return CreateWebhookResponseData::from($response->json());
    }

    public function listWebhooks(): ListWebhookResponseData
    {
        $path = 'webhooks';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
        ->withBasicAuth($this->secretKey, '')
        ->get($this->paymongoUrl.$path, []);

        return ListWebhookResponseData::from($response->json());
    }

    public function retrieveWebhook($webhookId): RetrieveWebhookResponseData
    {
        $path = 'webhooks/'.$webhookId;
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
            ->withBasicAuth($this->secretKey, '')
            ->get($this->paymongoUrl.$path, []);

        return RetrieveWebhookResponseData::from($response->json());
    }

    public function disableWebhook($webhookId): DisableWebhookResponseData
    {
        $path = 'webhooks/'.$webhookId.'/disable';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
            ->withBasicAuth($this->secretKey, '')
            ->get($this->paymongoUrl.$path, []);

        return DisableWebhookResponseData::from($response->json());
    }
}
