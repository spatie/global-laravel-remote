<?php

namespace Tests;

use App\Support\ConfigRepository;
use Tests\Support\TestCase;

class ConfigRepositoryTest extends TestCase
{
    public function test_can_store_and_forget_an_host(): void
    {
        $config = new ConfigRepository;

        $config->setHost('default', [
            'host' => 'example.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $this->assertIsArray($config->default);
        $this->assertSame('example.com', $config->default['host']);
        $this->assertSame('root', $config->default['user']);
        $this->assertSame(22, $config->default['port']);
        $this->assertSame('/', $config->default['path']);

        $config->forgetHost('default');
        $this->assertNull($config->default);
    }

    public function test_can_flush_all_hosts(): void
    {
        $config = new ConfigRepository;

        $config->setHost('example1', [
            'host' => 'example1.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $config->setHost('example2', [
            'host' => 'example2.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $config->flush();

        $this->assertCount(0, $config->all());
    }

    public function test_can_check_if_an_host_exists(): void
    {
        $config = new ConfigRepository;

        $config->setHost('default', [
            'host' => 'example1.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $this->assertNotNull($config->default);
    }
}
