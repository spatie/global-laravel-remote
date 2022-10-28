<?php

namespace App\Commands;

class ForgetCommand extends Command
{
    public $signature = 'forget {host}';

    public $description = 'Remove an item from the hosts';

    public function handle(): int
    {
        $host = $this->argument('host') ?? $this->ask('What is the host?');

        if (! $this->config->{$host}) {
            $this->components->error("{$host} does not exist");

            return self::FAILURE;
        }

        $this->config->forgetHost($host);
        $this->components->info("{$host} removed.");

        return self::SUCCESS;
    }
}
