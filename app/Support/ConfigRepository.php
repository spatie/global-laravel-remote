<?php

namespace App\Support;

use Spatie\Valuestore\Valuestore;

/**
 * @property-read ?array $hosts
 */
class ConfigRepository
{
    protected Valuestore $valuestore;

    public function __construct()
    {
        $path = "{$this->findHomeDirectory()}/.laravel-remote.json";

        $this->valuestore = Valuestore::make($path);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function all(): array
    {
        return $this->valuestore->all();
    }

    /**
     * @param  array<string, string|int>  $host
     */
    public function setHost(string $name, array $host = []): self
    {
        $this->valuestore->put([$name => $host]);

        return $this;
    }

    public function forgetHost(string $name): self
    {
        $this->valuestore->forget($name);

        return $this;
    }

    public function flush(): self
    {
        $this->valuestore->flush();

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->valuestore->has($name);
    }

    public function __get(string $name): mixed
    {
        return $this->valuestore->get($name);
    }

    protected function findHomeDirectory(): ?string
    {
        if (str_starts_with(PHP_OS, 'WIN')) {
            if (empty($_SERVER['HOMEDRIVE']) || empty($_SERVER['HOMEPATH'])) {
                return null;
            }

            $homeDirectory = $_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH'];

            return rtrim($homeDirectory, DIRECTORY_SEPARATOR);
        }

        if ($homeDirectory = getenv('HOME')) {
            return rtrim($homeDirectory, DIRECTORY_SEPARATOR);
        }

        return null;
    }
}
