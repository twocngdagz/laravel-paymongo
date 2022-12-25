<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response;

use \Spatie\LaravelData\Data as SpatieData;
class ResponseData extends SpatieData
{
    public function __construct(
       public Data $data
    ) {
    }
}
