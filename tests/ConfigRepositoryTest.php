<?php

use App\Support\ConfigRepository;

it('can store and forget a token', function () {
    $config = new ConfigRepository();

    $config->setHost('default', [
        'host' => 'example.com',
        'user' => 'root',
        'port' => 22,
        'path' => '/',
    ]);

    expect($config->default)->toBeArray();
    expect($config->default)
        ->host->toBe('example.com')
        ->user->toBe('root')
        ->port->toBe(22)
        ->path->toBe('/');

    $config->forgetHost('default');
    expect($config->default)->toBeNull();
});
