<?php

namespace Tests;

use App\Commands\FlushCommand;
use App\Support\ConfigRepository;
use Tests\Support\TestCase;

class FlushCommandTest extends TestCase
{
    public function test_removes_all_the_hosts_when_called_the_command(): void
    {
        $config = new ConfigRepository;

        $config->setHost('default', [
            'host' => 'example.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $this->assertCount(1, $config->all());

        $this->artisan(FlushCommand::class)->assertOk();

        $this->assertCount(0, $config->all());
    }
}
