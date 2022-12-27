<?php

namespace Twocngdagz\LaravelPaymongo\Jobs\Webhooks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twocngdagz\LaravelPaymongo\Facades\LaravelPaymongo;

class DisableWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(string $webhookId)
    {
        LaravelPaymongo::disableWebhook($webhookId);
    }
}
