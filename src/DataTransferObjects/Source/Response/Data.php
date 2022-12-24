<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Response;

use Spatie\DataTransferObject\DataTransferObject;

class Data extends DataTransferObject
{
    public string $id;

    public string $type;

    public AttributesData $attributes;
}
