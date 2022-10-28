<?php

namespace App\Commands;

use Spatie\Remote\Commands\RemoteCommand;

class GlobalRemoteCommand extends Command
{
    public $signature = 'global-remote {rawCommand} {--host=} {--raw} {--debug}';

    public $description = 'Execute commands on a remote server';

    public function handle(): int
    {
        $command = $this->argument('rawCommand');
        $host = $this->option('host');

        if (! $host) {
            $host = $this->selectFromHosts() ?? $this->promptToCreate();
        }

        if (! ($this->config->{$host} ?? null)) {
            $host = $this->promptToCreateNew($host);
        }

        config()->set('remote.hosts', $this->config->all());

        return $this->call(RemoteCommand::class, [
            'rawCommand' => $command,
            '--host' => $host,
            '--raw' => $this->option('raw'),
            '--debug' => $this->option('debug'),
        ]);
    }

    protected function promptToCreate(): string|false
    {
        $this->components->warn('There are no hosts created');

        if (! $this->components->confirm('Would you like to create one?')) {
            return false;
        }

        return $this->createHost();
    }

    protected function promptToCreateNew(): string|false
    {
        $this->components->warn('Host does not exist: '.$host);

        if (! $this->components->confirm('Would you like to create it?')) {
            return false;
        }

        return $this->createHost($host);
    }

    protected function selectFromHosts(): string|false
    {
        if (count($this->config->all()) === 0) {
            return false;
        }

        return $this->components->choice(
            'Please select one of the available hosts',
            array_map(
                fn ($host) => $host['user'].'@'.$host['host'].':'.$host['path'],
                $this->config->all()
            )
        );
    }

    protected function createHost(?string $name = null): string
    {
        if (! $name) {
            $name = $this->ask('Alias?');
        }

        $host = $this->ask('Host?', $name);
        $user = $this->ask('User?', 'root');
        $port = $this->ask('Port?', '22');
        $path = $this->ask('Path?', '/');

        $this->config->setHost($name, [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'path' => $path,
        ]);

        return $name;
    }
}
