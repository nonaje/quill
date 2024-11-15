<?php

declare(strict_types=1);

namespace Quill\Contracts\Request;

use Psr\Http\Message\ServerRequestInterface;

interface RequestInterface
{
    public function route(string $key, mixed $default = null): mixed;

    public function get(string $key, mixed $default = null): mixed;

    public function all(): mixed;

    public function getPsrRequest(): ServerRequestInterface;
}
