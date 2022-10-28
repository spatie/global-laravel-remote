<?php

use App\Commands\ForgetCommand;
use App\Support\ConfigRepository;

it('removes an host when called the command', function () {
    $config = new ConfigRepository();

    $config->setHost('default', [
        'host' => 'example.com',
        'user' => 'root',
        'port' => 22,
        'path' => '/',
    ]);

    expect($config->default['host'])->toBe('example.com');

    $this->artisan(ForgetCommand::class, [
        'host' => 'default',
    ])->assertOk();

    expect($config->default)->toBeNull();
});

it('returns a failure if there is no host found', function () {
    $this->artisan(ForgetCommand::class, [
        'host' => 'default',
    ])->assertFailed();
});
