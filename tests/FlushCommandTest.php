<?php

use App\Commands\FlushCommand;
use App\Support\ConfigRepository;

it('removes all the hosts when called the command', function () {
    $config = new ConfigRepository();

    $config->setHost('default', [
        'host' => 'example.com',
        'user' => 'root',
        'port' => 22,
        'path' => '/',
    ]);

    expect($config->all())->toHaveCount(1);

    $this->artisan(FlushCommand::class)->assertOk();

    expect($config->all())->toHaveCount(0);
});
