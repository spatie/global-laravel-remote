<?php

namespace App\Commands;

class FlushCommand extends Command
{
    public $signature = 'flush';

    public $description = 'Removes all hosts';

    public function handle(): int
    {
        $this->config->flush();
        $this->components->info('All hosts removed');

        return self::SUCCESS;
    }
}
