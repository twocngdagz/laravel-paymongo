<?php

namespace Twocngdagz\LaravelPaymongo\Exceptions;

use Exception;

class PaymongoUnauthorizedException extends Exception
{
    protected $code = 401;

    protected $message = 'Unauthorized';

    public function __construct(?string $message = null)
    {
        $this->message = $message ?: $this->message;
        parent::__construct($this->message, $this->code);
    }
}
