<?php

namespace Twocngdagz\LaravelPaymongo\Exceptions;

use Exception;

class PaymongoBadRequestException extends Exception
{
    protected $code = 400;

    protected $message = 'Bad Request';

    public function __construct(?string $message = null)
    {
        $this->message = $message ?: $this->message;
        parent::__construct($this->message, $this->code);
    }
}
