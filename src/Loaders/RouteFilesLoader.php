<?php

namespace Quill\Loaders;

use Quill\Contracts\Loader\FilesLoader;
use Quill\Contracts\Router\RouterInterface;
use Quill\Support\Traits\Singleton;

class RouteFilesLoader implements FilesLoader
{
    use Singleton;

    protected function __construct(private readonly RouterInterface $router) { }

    public function load(string ...$filenames): void
    {
        $routesPath = path()->applicationFile('routes');

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
