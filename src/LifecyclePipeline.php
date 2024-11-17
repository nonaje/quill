<?php

declare(strict_types=1);

namespace Quill;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Support\Traits\Singleton;
use Quill\Contracts\Lifecycle\LifecyclePipelineInterface;

final class LifecyclePipeline implements RequestHandlerInterface, LifecyclePipelineInterface
{
    use Singleton;

    private ServerRequestInterface $request;
    private RequestHandlerInterface $handler;

    /** @var MiddlewareInterface[] $middlewares */
    private array $middlewares;

    public function send(ServerRequestInterface $request): LifecyclePipelineInterface
    {
        $this->request = $request;

        return $this;
    }

    /** @inheritDoc */
    public function through(array $middlewares): LifecyclePipelineInterface
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function to(RequestHandlerInterface $handler): LifecyclePipelineInterface
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->handle($this->request);
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->middlewares)) {
            return $this->handler->handle($request);
        }

        return array_shift($this->middlewares)->process($request, $this);
    }
}
