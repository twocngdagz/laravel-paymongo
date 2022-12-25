<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Source\Request;

use Spatie\LaravelData\Data as SpatieData;

class RedirectData extends SpatieData
{
    public function __construct(
        public string $success,
        public string $failed
    ) {
    }
}
