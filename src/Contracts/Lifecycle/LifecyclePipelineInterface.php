<?php

declare(strict_types=1);

namespace Quill\Contracts\Lifecycle;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface LifecyclePipelineInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return LifecyclePipelineInterface
     */
    public function send(ServerRequestInterface $request): LifecyclePipelineInterface;

    /**
     * @param MiddlewareInterface[] $middlewares
     * @return LifecyclePipelineInterface
     */
    public function through(array $middlewares): LifecyclePipelineInterface;

    /**
     * @param RequestHandlerInterface $handler
     * @return LifecyclePipelineInterface
     */
    public function to(RequestHandlerInterface $handler): LifecyclePipelineInterface;

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
