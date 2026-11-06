<?php

namespace Tests;

use App\Commands\GlobalRemoteCommand;
use App\Support\ConfigRepository;
use Mockery as m;
use Spatie\Remote\Commands\RemoteCommand;
use Tests\Support\TestCase;

class GlobalRemoteCommandTest extends TestCase
{
    public function test_runs_the_remote_command_for_an_host(): void
    {
        (new ConfigRepository)->setHost('default', [
            'host' => 'example.com',
            'user' => 'root',
            'port' => 22,
            'path' => '/',
        ]);

        $this->swap(RemoteCommand::class, $mock = m::mock(RemoteCommand::class));

        $mock
            ->shouldIgnoreMissing()
            ->shouldReceive('run')
            ->once()
            ->withArgs(fn ($input) => $input->getParameterOption('rawCommand') === 'test' &&
                $input->getParameterOption('--host') === 'default'
            );

        $this->artisan(GlobalRemoteCommand::class, [
            'rawCommand' => 'test',
            '--host' => 'default',
        ]);
    }
}
