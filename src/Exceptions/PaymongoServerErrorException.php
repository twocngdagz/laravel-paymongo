<?php

namespace Twocngdagz\LaravelPaymongo\Exceptions;

use Exception;

class PaymongoServerErrorException extends Exception
{
    protected $code = 400;

    protected $message = 'Server Error';

    public function __construct(?string $message = null)
    {
        $this->message = $message ?: $this->message;
        parent::__construct($this->message, $this->code);
    }
}
