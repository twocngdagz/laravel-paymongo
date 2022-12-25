<?php

namespace Twocngdagz\LaravelPaymongo\DataObjects\Webhook\Response\Lists;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data as SpatieData;
use Spatie\LaravelData\DataCollection;

class ResponseData extends SpatieData
{
    public function __construct(
        #[DataCollectionOf(Data::class)]
        public DataCollection $data
    ) {
    }
}
