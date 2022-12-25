<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Request;

use Spatie\LaravelData\Data as SpatieData;

class Data extends SpatieData
{
    public function __construct(
        public AttributesData $attributes
    ) {
    }
}
