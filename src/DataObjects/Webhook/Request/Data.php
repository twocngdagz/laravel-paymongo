<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request;

use Spatie\LaravelData\Data as SpatieData;

class Data extends SpatieData
{
    public function __construct(
        public AttributesData $attributes
    ) {
    }
}
