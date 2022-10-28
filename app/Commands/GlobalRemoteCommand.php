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

        /** @var string|null */
        $host = $this->option('host');

        if (! $host) {
            $host = $this->selectFromHosts() ?? $this->promptToCreate();
        }

        if (! $this->config->has($host ?? '')) {
            $host = $this->promptToCreate($host);
        }

        if (! $host) {
            return self::FAILURE;
        }

        config()->set('remote.hosts', $this->config->all());

        return $this->call(RemoteCommand::class, [
            'rawCommand' => $command,
            '--host' => $host,
            '--raw' => $this->option('raw'),
            '--debug' => $this->option('debug'),
        ]);
    }

    protected function promptToCreate(?string $alias = null): string|null
    {
        if ($alias) {
            $this->components->warn("<options=bold>{$alias}</> does not exist.");
        } else {
            $this->components->warn('There are no hosts created');
        }

        if (! $this->components->confirm('Would you like to create one?')) {
            return null;
        }

        return $this->createHost($alias);
    }

    protected function selectFromHosts(): string|null
    {
        if (count($this->config->all()) === 0) {
            return null;
        }

        return $this->components->choice(
            'Please select one of the available hosts',
            array_map(
                fn ($host) => $host['user'].'@'.$host['host'].':'.$host['path'],
                $this->config->all()
            )
        );
    }

    protected function createHost(?string $alias = null): string
    {
        if (! $alias) {
            $alias = $this->ask('Alias?');
        }

        $host = $this->ask('Host?', $alias);
        $user = $this->ask('User?', 'root');
        $port = $this->ask('Port?', '22');
        $path = $this->ask('Path?', '/');

        $this->config->setHost($alias, [
            'host' => $host,
            'port' => (int) $port,
            'user' => $user,
            'path' => $path,
        ]);

        return $alias;
    }
}
