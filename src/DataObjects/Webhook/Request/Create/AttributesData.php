<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create;

use Spatie\LaravelData\Data as SpatieData;

class AttributesData extends SpatieData
{
    public function __construct(
        public string $url,
        public array $events
    ) {
    }
}
