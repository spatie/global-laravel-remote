<?php

namespace App\Support;

use Spatie\Valuestore\Valuestore;

/**
 * @property-read ?string $accessToken
 * @property-read ?string $gitHubUsername
 */
class ConfigRepository
{
    protected Valuestore $valuestore;

    public function __construct()
    {
        $path = "{$this->findHomeDirectory()}/.laravel-remote.json";

        $this->valuestore = Valuestore::make($path);
    }

    public function setHost(string $name, array $host = []): self
    {
        $this->valuestore->put([
            'hosts' => [
                $name => $host,
            ],
        ]);

        return $this;
    }

    public function __get(string $name): mixed
    {
        return $this->valuestore->get($name);
    }

    public function flush(): self
    {
        $this->valuestore->flush();

        return $this;
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
