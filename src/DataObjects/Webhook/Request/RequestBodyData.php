<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request;

use Spatie\LaravelData\Data as SpatieData;

class RequestBodyData extends SpatieData
{
    public function __construct(
        public Data $data
    ) {
    }
}
