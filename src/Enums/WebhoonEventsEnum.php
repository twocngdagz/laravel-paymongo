<?php

namespace Twocngdagz\LaravelPaymongo\Enums;

enum WebhookEventsEnum: string
{
    case SOURCE_CHARGEABLE = 'source.chargeable';
    case PAYMENT_PAID = 'payment.paid';
    case PAYMENT_FAILED = 'payment.failed';
}
