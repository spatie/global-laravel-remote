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
        $host = $this->getHost();

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

    protected function getHost(): string|null
    {
        /** @var string|null */
        $host = $this->option('host');

        if (! $host) {
            $host = $this->selectFromHosts() ?? $this->promptToCreate();
        }

        if (! $this->config->has($host ?? '')) {
            $host = $this->promptToCreate($host);
        }

        return $host;
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

        $items = collect($this->config->all())->map(fn ($item, $key) => [
            'key' => $key,
            ...$item,
        ])->values();

        $items->push([
            'key' => '[new]',
            'label' => '<span class="text-gray">Create new</span>',
        ]);

        $active = $this->select('Please select one of the available hosts:', $items->all());

        return $active['key'] === '[new]' ? $this->createHost() : $active['key'];
    }

    protected function createHost(?string $alias = null): string
    {
        if (! $alias) {
            $alias = $this->ask('Alias?');
        }

        $host = $this->ask('Host?', $alias);
        $user = $this->ask('User?', 'forge');
        $port = $this->ask('Port?', '22');
        $path = $this->ask('Path?', "/home/forge/{$host}");

        $this->config->setHost($alias, [
            'host' => $host,
            'port' => (int) $port,
            'user' => $user,
            'path' => $path,
        ]);

        return $alias;
    }
}
