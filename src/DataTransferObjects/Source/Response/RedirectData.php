<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Response;

use Spatie\DataTransferObject\DataTransferObject;

class RedirectData extends DataTransferObject
{
    public string $checkout_url;

    public string $failed;

    public string $success;
}
