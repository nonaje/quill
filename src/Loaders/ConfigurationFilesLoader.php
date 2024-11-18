<?php

declare(strict_types=1);

namespace Quill\Loaders;

use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Contracts\Loader\FilesLoader;
use Quill\Support\Path;
use Quill\Support\Singleton;

final class ConfigurationFilesLoader implements FilesLoader
{
    use Singleton;

    protected function __construct(
        private readonly ConfigurationInterface $config,
    ) { }

    public function load(string ...$filenames): void
    {
        $configurationPath = Path::toConfig();
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
