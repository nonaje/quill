<?php

declare(strict_types=1);

namespace Quill\Loaders;

use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Contracts\Loader\FilesLoader;
use Quill\Support\Path;

final readonly class ConfigurationFilesLoader implements FilesLoader
{
    public function __construct(private ConfigurationInterface $config)
    {
    }

    public function load(string ...$filenames): void
    {
        $configurationPath = Path::toFile('config');
        $filename ??= $configurationPath;

        if (is_file($filename)) {
            $this->config->put(
                key: substr(basename($filename), 0, -4),
                value: require_once $filename
            );

            return;
        }

        if (is_dir($filename)) {
            $configurationFiles = scandir($filename);
            if (is_array($configurationFiles)) {
                $configurationFiles = array_diff($configurationFiles, ['.', '..']);
            }

            foreach ($configurationFiles as $filename) {
                // Removing the .php
                $name = substr(basename($filename), 0, -4);
                $this->config->put(
                    key: $name,
                    value: require "$configurationPath/$filename"
                );
            }
        }
    }
}
