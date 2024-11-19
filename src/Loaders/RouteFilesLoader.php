<?php

namespace Quill\Loaders;

use Quill\Contracts\Loader\FilesLoader;
use Quill\Contracts\Router\RouterInterface;
use Quill\Support\Path;

final readonly class RouteFilesLoader implements FilesLoader
{
    public function __construct(private RouterInterface $router) { }

    public function load(string ...$filenames): void
    {
        $routesPath = Path::toFile('routes');

        if (is_dir($routesPath)) {
            $routesFiles = scandir($routesPath);

            if (is_array($routesFiles)) {
                $routesFiles = array_diff($routesFiles, ['.', '..']);
            }

            foreach ($routesFiles as $filename) {
                $routes = require_once "$routesPath/$filename";

                if (is_callable($routes)) {
                    $routes($this->router);
                }
            }
        }
    }
}
