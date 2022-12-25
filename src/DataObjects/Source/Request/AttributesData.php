<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Source\Request;

use Spatie\LaravelData\Data as SpatieData;

class AttributesData extends SpatieData
{
    public function __construct(
        public int $amount,
        public string $currency,
        public string $type,
        public RedirectData $redirect
    ) {
    }
}
