<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Source\Request;

use Spatie\LaravelData\Data as SpatieData;

class RequestBodyData extends SpatieData
{
    public function __construct(
        public Data $data
    ) {
    }
}
