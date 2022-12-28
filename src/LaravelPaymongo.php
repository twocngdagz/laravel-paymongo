<?php

namespace Twocngdagz\LaravelPaymongo;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Request\RequestBodyData as CreateSourceRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Source\Response\ResponseData as CreateSourceResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create\RequestBodyData as CreateWebhookRequestBodyData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Update\RequestBodyData as UpdateWebhookRequestData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Create\ResponseData as CreateWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Disable\ResponseData as DisableWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Enable\ResponseData as EnableWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists\ResponseData as ListWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Retrieve\ResponseData as RetrieveWebhookResponseData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Update\ResponseData as UpdateWebhookResponseData;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoBadRequestException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoForbiddenException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoMissingKeyException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoNotFoundException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoServerErrorException;
use Twocngdagz\LaravelPaymongo\Exceptions\PaymongoUnauthorizedException;

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
        $path = $this->paymongoUrl.'sources';
        $response = $this->request(url: $path, method: 'post', body: $body->toArray());

        return CreateSourceResponseData::from($response->json());
    }

    public function createWebhook(CreateWebhookRequestBodyData $body): CreateWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks';
        $response = $this->request(url: $path, method: 'post', body: $body->toArray());

        return CreateWebhookResponseData::from($response->json());
    }

    public function listWebhooks(): ListWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks';
        $response = $this->request(url: $path, method: 'get');

        return ListWebhookResponseData::from($response->json());
    }

    public function retrieveWebhook(string $webhookId): RetrieveWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks/'.$webhookId;
        $response = $this->request(url: $path, method: 'get');

        return RetrieveWebhookResponseData::from($response->json());
    }

    public function disableWebhook(string $webhookId): DisableWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks/'.$webhookId.'/disable';
        $response = $this->request(url: $path, method: 'post');

        return DisableWebhookResponseData::from($response->json());
    }

    public function enableWebhook(string $webhookId): EnableWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks/'.$webhookId.'/enable';
        $response = $this->request(url: $path, method: 'post');

        return EnableWebhookResponseData::from($response->json());
    }

    public function updateWebhook(UpdateWebhookRequestData $body, string $webhookId): UpdateWebhookResponseData
    {
        $path = $this->paymongoUrl.'webhooks/'.$webhookId;
        $response = $this->request(url: $path, method: 'put', body: $body->toArray());

        return UpdateWebhookResponseData::from($response->json());
    }

    protected function request(string $url, string $method, array $body = [])
    {
        $this->init();
        try {
            return Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])
                ->withBasicAuth($this->secretKey, '')
                ->{$method}($url, $body)->throw();
        } catch (RequestException $e) {
            $errorMessage = $this->extractErrorMessage($e);

            match ($e->getCode()) {
                400 => throw new PaymongoBadRequestException($errorMessage),
                401 => throw new PaymongoUnauthorizedException($errorMessage),
                403 => throw new PaymongoForbiddenException($errorMessage),
                404 => throw new PaymongoNotFoundException($errorMessage),
                500 => throw new PaymongoServerErrorException($errorMessage),
                default => throw $e,
            };
        }
    }

    protected function extractErrorMessage(RequestException $e): ?string
    {
        $errors = $e->response->json();
        if (! is_array($errors)) {
            return null;
        }

        return $errors['errors'][0]['detail'] ?? null;
    }
}
