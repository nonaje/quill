<?php

declare(strict_types=1);

namespace Quill\Contracts\Router;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Enums\Http\HttpMethod;

interface RouteInterface
{
    public function middleware(string|array|Closure|MiddlewareInterface $middleware): static;

    public function uri(): string;

    public function method(): HttpMethod;

    public function target(): Closure|array|string;

    public function params(): array;

    public function getMiddlewares(): MiddlewareStoreInterface;
}
