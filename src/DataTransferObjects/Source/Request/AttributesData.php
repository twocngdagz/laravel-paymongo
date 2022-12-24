<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Request;

use Spatie\DataTransferObject\DataTransferObject;

class AttributesData extends DataTransferObject
{
    public int $amount;

    public string $currency;

    public string $type;

    public RedirectData $redirect;
}
