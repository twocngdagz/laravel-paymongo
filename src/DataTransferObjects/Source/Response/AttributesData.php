<?php

namespace Twocngdagz\LaravelPaymongo\DataTransferObjects\Source\Response;

use Spatie\DataTransferObject\DataTransferObject;

class AttributesData extends DataTransferObject
{
    public int $amount;

    public ?string $billing;

    public string $currency;

    public ?string $description;

    public bool $livemode;

    public RedirectData $redirect;

    public ?string $statement_descriptor;

    public ?string $status;

    public string $type;

    public ?string $metadata;

    public string $created_at;

    public string $updated_at;
}
