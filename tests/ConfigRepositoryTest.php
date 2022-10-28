<?php

use App\Support\ConfigRepository;

it('can store and forget an host', function () {
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

it('can flush all hosts', function () {
    $config = new ConfigRepository();

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

    expect($config->all())->toHaveCount(0);
});

it('can check if an host exists', function () {
    $config = new ConfigRepository();

    $config->setHost('default', [
        'host' => 'example1.com',
        'user' => 'root',
        'port' => 22,
        'path' => '/',
    ]);

    expect($config->default)->not->toBeNull();
});
