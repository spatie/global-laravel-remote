<?php

use App\Commands\GlobalRemoteCommand;
use App\Support\ConfigRepository;
use Mockery as m;
use Spatie\Remote\Commands\RemoteCommand;

it('runs the remote command for an host', function () {
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
});

it('runs the remote command without providing the host option', function () {
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
    ])->expectsQuestion('Please select one of the available hosts', 'default');
});

it('creates an host and runs the remote command', function () {
    $config = new ConfigRepository();

    expect($config->all())->toHaveCount(0);

    $this->artisan(GlobalRemoteCommand::class, ['rawCommand' => 'test'])
        ->expectsConfirmation('Would you like to create one?', 'yes')
        ->expectsQuestion(question('Alias?'), 'default')
        ->expectsQuestion(question('Host?', 'default'), 'default')
        ->expectsQuestion(question('User?', 'root'), 'root')
        ->expectsQuestion(question('Port?', '22'), '22')
        ->expectsQuestion(question('Path?', '/'), '/');

    expect((object) $config->default)
        ->host->toBe('default')
        ->user->toBe('root')
        ->port->toBe(22)
        ->path->toBe('/');
});
