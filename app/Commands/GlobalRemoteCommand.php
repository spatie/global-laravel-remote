<?php

namespace App\Commands;

use Spatie\Remote\Commands\RemoteCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class GlobalRemoteCommand extends Command
{
    public $signature = 'global-remote {rawCommand?} {--host=} {--raw} {--debug}';

    public $description = 'Execute commands on a remote server';

    public function handle(): int
    {
        $command = $this->argument('rawCommand');

        if (! $command) {
            $command = text('What command do you want to run on your server?', required: true);
        }

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

    protected function getHost(): ?string
    {
        /** @var string|null */
        $host = $this->option('host');

        if (! $host) {
            $host = $this->selectFromHosts() ?? $this->promptToCreate();
        }

        if ($host && ! $this->config->has($host)) {
            $host = $this->promptToCreate($host);
        }

        return $host;
    }

    protected function promptToCreate(?string $alias = null): ?string
    {
        if ($alias) {
            $this->components->warn("<options=bold>{$alias}</> does not exist.");
        } else {
            $this->components->warn('There are no hosts created');
        }

        if (! confirm('Would you like to create one?')) {
            return null;
        }

        return $this->createHost($alias);
    }

    protected function selectFromHosts(): ?string
    {
        if (count($this->config->all()) === 0) {
            return null;
        }

        $hosts = collect($this->config->all())
            ->mapWithKeys(fn ($item, $key) => [$key => $item['host']])
            ->all();

        $active = select(
            label: 'Please select one of the available hosts:',
            options: [
                ...$hosts,
                '[new]' => 'Create new',
            ],
        );

        return $active === '[new]'
            ? $this->createHost()
            : $active;
    }

    protected function createHost(?string $alias = null): string
    {
        if (! $alias) {
            $alias = text('Provide the alias for your host', required: true);
        }

        $host = text(
            label: 'Provide the host',
            default: $alias,
            required: true,
            hint: 'Ex. ssh.laravel.com'
        );

        $user = text(
            label: 'Username',
            required: true,
            hint: 'Ex. forge',
        );

        $port = text(
            label: 'Port',
            default: '22',
            required: true,
        );

        $path = text(
            label: 'Path to the laravel codebase',
            default: "/home/forge/{$host}",
            required: true,
        );

        $this->config->setHost($alias, [
            'host' => $host,
            'port' => (int) $port,
            'user' => $user,
            'path' => $path,
        ]);

        return $alias;
    }
}
