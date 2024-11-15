<?php

declare(strict_types=1);

namespace Quill\Request;

use Psr\Http\Message\ServerRequestInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Router\RouteInterface;
use Quill\Enums\Http\HttpMethod;
use Quill\Support\Traits\Singleton;

class Request implements RequestInterface
{
    use Singleton;

    protected function __construct(protected readonly ServerRequestInterface $psrRequest) { }

    public function getPsrRequest(): ServerRequestInterface
    {
        return $this->psrRequest;
    }

    public function route(string $key, mixed $default = null): mixed
    {
        return $this->getRoute()->params()[$key] ?? $default;
    }

    public function method(): HttpMethod
    {
        return HttpMethod::from(strtoupper($this->getPsrRequest()->getMethod()));
    }

    public function all(): mixed
    {
        // TODO: Implement method

        return [];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // TODO: Implement method

        $value = $default;

        return $value;
    }

    private function getRoute(): RouteInterface
    {
        return $this->getPsrRequest()->getAttribute('route');
    }

    private function json(string $key, mixed $default): mixed
    {
        $body = json_decode($this->getPsrRequest()->getBody()->getContents(), true) ?? $default;

        return $key ? ($body[$key] ?? $default) : $body;
    }
}
