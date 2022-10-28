<?php

use App\Commands\Command;
use App\Support\ConfigRepository;
use Termwind\HtmlRenderer;

uses(\Tests\Support\TestCase::class)
    ->beforeEach(fn () => (new ConfigRepository)->flush())
    ->in(__DIR__);

function question(string $question, ?string $default = null)
{
    return (string) (new HtmlRenderer)
        ->parse(Command::getAskTemplate($question, $default));
}
