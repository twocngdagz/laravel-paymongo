<?php

namespace Twocngdagz\LaravelPaymongo\Models;

use Illuminate\Database\Eloquent\Model;
use Twocngdagz\LaravelPaymongo\Database\Factories\WebhookFactory;

class Webhook extends Model
{
    protected $fillable = [
        'webhook_id',
        'secret_key',
        'url',
        'events',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'events' => 'array',
    ];

    protected static function newFactory()
    {
        return WebhookFactory::new();
    }
}
