<?php

namespace Tests\Support;

use App\Support\ConfigRepository;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        (new ConfigRepository)->flush();
    }

    public static function tearDownAfterClass(): void
    {
        (new ConfigRepository)->flush();

        parent::tearDownAfterClass();
    }
}
