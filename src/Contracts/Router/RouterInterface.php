<?php

declare(strict_types=1);

namespace Quill\Contracts\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Router\Route;

/**
 * @method Route get(string $uri, Closure|array|string $target)
 * @method Route head(string $uri, Closure|array|string $target)
 * @method Route post(string $uri, Closure|array|string $target)
 * @method Route put(string $uri, Closure|array|string $target)
 * @method Route patch(string $uri, Closure|array|string $target)
 * @method Route delete(string $uri, Closure|array|string $target)
 */
interface RouterInterface
{
    /**
     * Register a new routes group
     *
     * @param string $prefix
     * @param Closure $routes
     * @return RouteGroupInterface
     */
    public function group(string $prefix, Closure $routes): RouteGroupInterface;

    /**
     * Returns all registered routes.
     *
     * The routes within groups are "compiled" and returned as particular routes
     * with their full path and the middlewares, both individual and those inherited by the group.
     *
     * @return RouteInterface[]
     */
    public function routes(): array;
}
