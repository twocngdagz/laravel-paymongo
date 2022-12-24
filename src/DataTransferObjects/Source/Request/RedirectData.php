<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Request;

use Spatie\DataTransferObject\DataTransferObject;

class RedirectData extends DataTransferObject
{
    public string $success;

    public string $failed;
}
