<?php

namespace Twocngdagz\LaravelPaymongo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Twocngdagz\LaravelPaymongo\LaravelPaymongo
 */
class LaravelPaymongo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Twocngdagz\LaravelPaymongo\LaravelPaymongo::class;
    }
}
