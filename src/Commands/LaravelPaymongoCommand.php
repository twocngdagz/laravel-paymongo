<?php

namespace Twocngdagz\LaravelPaymongo\Commands;

use Illuminate\Console\Command;

class LaravelPaymongoCommand extends Command
{
    public $signature = 'laravel-paymongo';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
