<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response;

use Spatie\LaravelData\Data;

class AttributesData extends Data
{
    public function __construct(
        public bool $livemode,
        public string $secret_key,
        public string $status,
        public string $url,
        public array $events,
        public int $created_at,
        public int $updated_at
    ) {
    }
}
