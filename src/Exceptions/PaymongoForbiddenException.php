<?php

namespace Twocngdagz\LaravelPaymongo\Exceptions;

use Exception;

class PaymongoForbiddenException extends Exception
{
    protected $code = 403;

    protected $message = 'Forbidden';

    public function __construct(?string $message = null)
    {
        $this->message = $message ?: $this->message;
        parent::__construct($this->message, $this->code);
    }
}
