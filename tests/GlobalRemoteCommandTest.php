<?php

use App\Commands\GlobalRemoteCommand;
use App\Support\ConfigRepository;
use Spatie\Remote\Commands\RemoteCommand;

it('runs the remote command for an host', function () {
    (new ConfigRepository)->setHost('default', [
        'host' => 'example.com',
        'user' => 'root',
        'port' => 22,
        'path' => '/',
    ]);

    $mock = Mockery::mock(RemoteCommand::class);
    $this->swap(RemoteCommand::class, $mock);

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
});
