<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Create;

use Spatie\LaravelData\Data as SpatieData;

class ResponseData extends SpatieData
{
    public function __construct(
       public Data $data
    ) {
    }
}
