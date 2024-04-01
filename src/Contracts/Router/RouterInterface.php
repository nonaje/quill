<?php

declare(strict_types=1);

namespace Quill\Contracts\Router;

use Closure;
use Quill\Router\Route;

/**
 * @method Route get(string $uri, Closure|array $target)
 * @method Route post(string $uri, Closure|array $target)
 * @method Route put(string $uri, Closure|array $target)
 * @method Route patch(string $uri, Closure|array $target)
 * @method Route delete(string $uri, Closure|array $target)
 */
interface RouterInterface
{
    public function map(string $method, string $uri, Closure|array $target): RouteInterface;
}