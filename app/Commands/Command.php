<?php

namespace App\Commands;

use App\Support\ConfigRepository;
use LaravelZero\Framework\Commands\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use function Termwind\ask;
use function Termwind\renderUsing;

abstract class Command extends BaseCommand
{
    public function __construct(
        public ConfigRepository $config,
    ) {
        parent::__construct();
    }

    public function ask($question, $default = null)
    {
        renderUsing($this->output);

        $answer = ask(self::getAskTemplate($question, $default)) ?? $default;

        return $answer;
    }

    /**
     * @param  array<string, string|int>  $arguments
     */
    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        return $this->resolveCommand($command)->run(
            $this->createInputFromArguments($arguments), $output
        );
    }

    public static function getAskTemplate(string $question, ?string $default = null): string
    {
        $defaultText = $default ? "[<span class='text-yellow'>$default</span>]" : '';

        return <<<HTML
            <span class="ml-2">
                <span class="font-bold">{$question}</span> {$defaultText}<br>
                <span class="mr-1 font-bold">❯</span>
            </span>
        HTML;
    }
}
