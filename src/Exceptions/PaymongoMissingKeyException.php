<?php

namespace Twocngdagz\LaravelPaymongo\Exceptions;

class PaymongoMissingKeyException extends \Exception
{
    protected $code = 400;
    protected $message = 'Missing Paymongo API Keys!!!';


    public function __construct(?string $message = null)
    {
        $this->message = $message ?: $this->message;
        parent::__construct($this->message, $this->code);
    }


}
