<?php

declare(strict_types=1);

namespace Quill\Loaders;

use InvalidArgumentException;
use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Contracts\Loader\FilesLoader;
use Quill\Support\Path;

final readonly class DotEnvLoader implements FilesLoader
{
    public function __construct(private ConfigurationInterface $config)
    {
    }

    public function load(string ...$filenames): void
    {
        if (count($filenames) > 1) {
            throw new InvalidArgumentException('Only one dotenv file can be loaded.');
        }

        $filename ??= Path::toFile('.env');

        if (!str_ends_with($filename, '.env')) {
            throw new InvalidArgumentException("File: {$filename} must be a .env file");
        }

        if (!file_exists($filename)) {
            return;
        }

        // Load .env into configuration items
        $env = parse_ini_file($filename);
        $this->config->put('env', array_combine(array_map('strtolower', array_keys($env)), array_values($env)));
    }
}
