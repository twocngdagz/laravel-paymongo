<?php

namespace Twocngdagz\LaravelPaymongo;

use Illuminate\Support\Facades\Http;
use Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Request\RequestBodyData;
use Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Response\ResponseData;
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

    public function createSource(RequestBodyData $body): ResponseData
    {
        $path = 'sources';
        $this->init();
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])
        ->withBasicAuth($this->publicKey, '')
        ->post($this->paymongoUrl.$path, $body->toArray());

        return new ResponseData($response->json());
    }
}
