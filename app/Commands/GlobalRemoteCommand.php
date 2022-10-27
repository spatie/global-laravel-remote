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

    protected function createHost($name)
    {
        $host = $this->ask('What is your host?', 'localhost');
        $user = $this->ask('What is your user?', 'root');
        $port = $this->ask('What is your port?', 22);
        $path = $this->ask('What is the path', '/');

        $this->config->setHost($name, [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'path' => $path,
        ]);
    }
}
