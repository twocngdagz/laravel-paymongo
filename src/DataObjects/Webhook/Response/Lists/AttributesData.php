<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists;

use Spatie\LaravelData\Data;

class AttributesData extends Data
{
    public function __construct(
       public string $livemode,
       public string $secret_key,
       public string $status,
       public string $url,
       public array $events,
       public int $created_at,
       public int $updated_at
    ) {
    }
}
