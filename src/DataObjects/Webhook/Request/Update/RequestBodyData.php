<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Update;

use Spatie\LaravelData\Data as SpatieData;
use Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Request\Create\Data;

class RequestBodyData extends SpatieData
{
    public function __construct(
       public Data $data
    ) {
    }
}
