<?php

declare(strict_types=1);

namespace Quill\Contracts\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;

interface RouteGroupInterface
{
    /** @return RouteInterface[] */
    public function routes(): array;

    public function middleware(string|array|Closure|MiddlewareInterface $middleware): static;

    public function getMiddlewares(): MiddlewareStoreInterface;
}
