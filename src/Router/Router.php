<?php

declare(strict_types=1);

namespace Quill\Router;

use \Closure;
use LogicException;
use Quill\Contracts\Router\MiddlewareStoreInterface;
use Quill\Contracts\Router\RouteGroupInterface;
use Quill\Contracts\Router\RouteInterface;
use Quill\Contracts\Router\RouterInterface;
use Quill\Contracts\Router\RouteStoreInterface;
use Quill\Enums\Http\HttpMethod;
use Quill\Support\Traits\Middlewares;

class Router implements RouterInterface
{
    protected function __construct(
        private readonly RouteStoreInterface        $routes,
        private readonly string                     $prefix = ''
    ) { }

    /** @inheritDoc */
    public function routes(): array
    {
        return $this->routes->all();
    }

    /** @inheritDoc */
    public function group(string $prefix, Closure $routes): RouteGroupInterface
    {
        $prefix = $this->prefix . '/' . trim($prefix, '/');

        return $this->routes->addGroup(new RouteGroup(
            prefix: $prefix,
            routes: $routes,
            router: new self(
                routes: new RouteStore,
                prefix: $prefix
            ),
            middlewares: new MiddlewareStore(),
        ));
    }

    /**
     * Register the routes with their respective http method
     *
     * @param string $method
     * @param array $arguments
     * @return RouteInterface
     */
    public function __call(string $method, array $arguments = []): RouteInterface
    {
        if (! in_array(strtoupper($method), HttpMethod::values())) {
            throw new LogicException("Undefined method " . self::class . "@$method");
        }

        return $this->map(HttpMethod::from(strtoupper($method)), ...$arguments);
    }

    /**
     * Add a new route to the collection
     *
     * @param HttpMethod $method
     * @param string $uri
     * @param Closure|array|string $target
     * @return RouteInterface
     */
    protected function map(HttpMethod $method, string $uri, Closure|array|string $target): RouteInterface
    {
        $uri = $this->prefix . '/' . trim($uri, '/');

        return $this->routes->add(new Route(
            uri: $uri,
            method: $method,
            target: $target,
            middlewares: new MiddlewareStore(),
        ));
    }
}
