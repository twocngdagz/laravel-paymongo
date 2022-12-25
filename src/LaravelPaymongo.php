<?php

namespace Twocngdagz\LaravelPaymongo;

use Illuminate\Support\Facades\Http;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Request\RequestBodyData as SourceRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Response\ResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\RequestBodyData as WebhookRequestBodyData;
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

    public function createSource(SourceRequestBodyData $body): ResponseData
    {
        $path = 'sources';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])
        ->withBasicAuth($this->publicKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return ResponseData::from($response->json());
    }


    public function create(WebhookRequestBodyData $body)
    {
        $path = 'webhooks';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-typ' => 'application/json',
        ])
        ->withBasicAuth($this->secretKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());
    }
}
