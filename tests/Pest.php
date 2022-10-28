<?php

use App\Support\ConfigRepository;

uses(\Tests\Support\TestCase::class)
    ->beforeEach(fn () => (new ConfigRepository)->flush())
    ->in(__DIR__);
