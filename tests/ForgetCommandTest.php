<?php

namespace Tests;

use App\Commands\ForgetCommand;
use App\Support\ConfigRepository;
use Tests\Support\TestCase;

class ForgetCommandTest extends TestCase
{
    public function test_removes_an_host_when_called_the_command(): void
    {
        $config = new ConfigRepository;

        $config->setHost('default', [
            'host' => 'example.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $this->assertSame('example.com', $config->default['host']);

        $this->artisan(ForgetCommand::class, [
            'host' => 'default',
        ])->assertOk();

        $this->assertNull($config->default);
    }

    public function test_returns_a_failure_if_there_is_no_host_found(): void
    {
        $this->artisan(ForgetCommand::class, [
            'host' => 'default',
        ])->assertFailed();
    }
}
