<?php

namespace App\Commands;

use App\Support\ConfigRepository;
use App\Support\Select;
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
     * @param  array<int, array<int|string, mixed>>  $options
     * @return array<string, mixed>
     */
    public function select(string $question, array $options = [], int $activeIndex = 0): array
    {
        $select = new Select(
            callback: fn ($options, $active) => self::getSelectTemplate($question, $options, $active),
            options: $options,
            activeIndex: $activeIndex
        );

        $this->trap([SIGTERM, SIGQUIT], fn () => $select->close());

        $select->render();

        return $select->getActive();
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

    /**
     * @param  array<int, array<int|string, mixed>>  $options
     * @param  array<int|string, mixed>  $active
     */
    public static function getSelectTemplate(string $question, array $options, array $active): string
    {
        $items = implode('', array_map(function ($option) use ($active) {
            $activeIcon = $active['key'] === $option['key'] ? '❯' : '';
            $label = $option['label'] ?? $option['key'];

            return <<<HTML
                <div>
                    <span class="mr-1 w-1 font-bold">{$activeIcon}</span>
                    <span>{$label}</span>
                </div>
            HTML;
        }, $options));

        return <<<HTML
            <div class="mt-1">
                <div class="ml-2 font-bold">{$question}</div>
                {$items}
            </div>
        HTML;
    }
}
