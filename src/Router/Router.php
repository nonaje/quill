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
    use Middlewares;

    protected function __construct(
        private readonly MiddlewareStoreInterface   $middlewares,
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
        $middlewares = $this->getMiddlewares()->all();
        $prefix = $this->prefix . '/' . trim($prefix, '/');

        $group = $this->routes->addGroup(new RouteGroup(
            prefix: $prefix,
            routes: $routes,
            router: new self(
                middlewares: new MiddlewareStore,
                routes: new RouteStore,
                prefix: $prefix
            ),
            middlewares: (new MiddlewareStore())->add($middlewares),
        ));

        $this->getMiddlewares()->reset();

        return $group;
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
        $enumMethod = HttpMethod::from(strtoupper($method));

        if (in_array($enumMethod->value, HttpMethod::values())) {
            return $this->map($enumMethod, ...$arguments);
        }

        throw new LogicException("Undefined method " . self::class . "@$method");
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
        $middlewares = $this->getMiddlewares()->all();
        $uri = $this->prefix . '/' . trim($uri, '/');

        $route = $this->routes->add(new Route(
            uri: $uri,
            method: $method,
            target: $target,
            middlewares: (new MiddlewareStore())->add($middlewares),
        ));

        $this->getMiddlewares()->reset();

        return $route;
    }
}
