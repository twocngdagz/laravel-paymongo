<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response;

use Spatie\LaravelData\Data as SpatieData;

class Data extends SpatieData
{
    public function __construct(
        public string $id,
        public string $type,
        public AttributesData $attributes
    ) {
    }
}
