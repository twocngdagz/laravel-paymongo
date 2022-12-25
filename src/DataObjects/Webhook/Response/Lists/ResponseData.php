<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists;

use Spatie\LaravelData\Data as SpatieData;
use Spatie\LaravelData\DataCollection;

class ResponseData extends SpatieData
{
    public function __construct(
        /** @var Data[] */
        public DataCollection $data
    ) {
    }
}
