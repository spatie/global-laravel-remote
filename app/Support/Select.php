<?php

namespace App\Support;

use Closure;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Termwind\HtmlRenderer;
use Termwind\Termwind;

/**
 * @internal
 */
final class Select
{
    const KEY_UP = "\033[A";

    const KEY_DOWN = "\033[B";

    const KEY_ENTER = "\n";

    private Cursor $cursor;

    private HtmlRenderer $renderer;

    private ConsoleSectionOutput $section;

    /** @var resource */
    private $stdin;

    private string $sttyMode;

    /**
     * Creates a new Select instance.
     *
     * @param  array<int, array<int|string, mixed>>  $options
     */
    public function __construct(
        private Closure $callback,
        private array $options,
        private int $activeIndex = 0,
        private ?OutputInterface $output = null,
    ) {
        $this->output ??= Termwind::getRenderer();

        /** @phpstan-ignore-next-line */
        $this->section = $this->output->section();

        $this->renderer = new HtmlRenderer();
        $this->cursor = new Cursor($this->output);

        $this->stdin = \defined('STDIN') ? \STDIN : fopen('php://input', 'r+');
    }

    /**
     * Renders the select.
     */
    public function render(): bool
    {
        $this->cursor->hide();

        /** @var resource */
        stream_set_blocking($this->stdin, false);
        $this->sttyMode = shell_exec('stty -g');

        shell_exec('stty cbreak -echo');

        $this->refresh();

        while (true) {
            if (! $this->shouldRefresh($key = fgets($this->stdin))) {
                continue;
            }

            if ($key === self::KEY_ENTER) {
                $this->close();

                return true;
            }

            $this->activeIndex = match ($key) {
                self::KEY_UP => max(0, --$this->activeIndex),
                self::KEY_DOWN => min(count($this->getOptions()) - 1, ++$this->activeIndex),
                default => $this->activeIndex,
            };

            $this->refresh();
        }
    }

    /**
     * Checks if the content needs to be updated.
     */
    private function shouldRefresh(bool|string $key): bool
    {
        return in_array($key, [self::KEY_ENTER, self::KEY_UP, self::KEY_DOWN], true);
    }

    /**
     * Returns the active selected.
     *
     * @return array<string, mixed>
     */
    public function getActive(): array
    {
        return $this->getOptions()[$this->activeIndex];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function refresh(): void
    {
        $html = call_user_func(
            $this->callback,
            $this->getOptions(),
            $this->getActive()
        );

        $html = $this->renderer->parse((string) $html);

        $this->section->clear();
        $this->section->write($html->toString());
    }

    public function close(): void
    {
        $this->cursor->show();
        shell_exec(sprintf('stty %s', $this->sttyMode));
        stream_set_blocking($this->stdin, true);
    }
}
