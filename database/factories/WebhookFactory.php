<?php

namespace Twocngdagz\LaravelPaymongo\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Twocngdagz\LaravelPaymongo\Models\Webhook;


class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition()
    {
        return [
            'webhook_id' => 'hook_' . faker()->uuid,
            'event' => randomElement()
        ];
    }
}

