<?php

namespace Spatie\GlobalLaravelRemote\Commands;

use Illuminate\Console\Command;

class GlobalLaravelRemoteCommand extends Command
{
    public $signature = 'global-laravel-remote';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
