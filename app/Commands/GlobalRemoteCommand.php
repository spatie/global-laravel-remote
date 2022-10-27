<?php

namespace App\Commands;

use Spatie\Remote\Commands\RemoteCommand;

class GlobalRemoteCommand extends Command
{
    public $signature = 'global-remote {rawCommand} {--host=} {--raw} {--debug}';

    public $description = 'Execute commands on a remote server';

    public function handle()
    {
        $command = $this->argument('rawCommand');
        $host = $this->option('host') ?? config('remote.default_host');

        if (! ($this->config->hosts[$host] ?? null)) {
            $this->components->warn('Host does not exist: '.$host);

            if (! $this->components->confirm('Would you like to create it?')) {
                return 0;
            }

            $this->createHost($host);
        }

        config()->set('remote.hosts', $this->config->hosts);

        return $this->call(RemoteCommand::class, [
            'rawCommand' => $command,
            '--host' => $host,
            '--raw' => $this->option('raw'),
            '--debug' => $this->option('debug'),
        ]);
    }

    protected function createHost(string $name): void
    {
        $host = $this->ask('Host?', 'localhost');
        $user = $this->ask('User?', 'root');
        $port = $this->ask('Port?', 22);
        $path = $this->ask('Path?', '/');

        $this->config->setHost($name, [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'path' => $path,
        ]);
    }
}
