<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Source\Response;

use Spatie\LaravelData\Data as SpatieData;

class RedirectData extends SpatieData
{
    public function __construct(
        public string $checkout_url,
        public string $failed,
        public string $success
    ) {
    }
}
