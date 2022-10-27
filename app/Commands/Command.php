<?php

namespace App\Commands;

use App\Support\ConfigRepository;
use LaravelZero\Framework\Commands\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    public function __construct(
        public ConfigRepository $config,
    ) {
        parent::__construct();
    }

    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        return $this->resolveCommand($command)->run(
            $this->createInputFromArguments($arguments), $output
        );
    }
}
