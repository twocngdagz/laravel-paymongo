<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Retrieve;

use Spatie\LaravelData\Data as SpatieData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists\Data;

class ResponseData extends SpatieData
{
    public function __construct(
       public Data $data
    ) {
    }
}
