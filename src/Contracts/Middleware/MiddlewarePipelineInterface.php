<?php

declare(strict_types=1);

namespace Quill\Contracts\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewarePipelineInterface
{
    /**
     * The request instance that will be sent through the middlewares and the handler
     *
     * @param ServerRequestInterface $request
     * @return MiddlewarePipelineInterface
     */
    public function send(ServerRequestInterface $request): MiddlewarePipelineInterface;

    /**
     * The array of middlewares that will receive the request
     *
     * @param MiddlewareInterface[] $middlewares
     * @return MiddlewarePipelineInterface
     */
    public function through(array $middlewares): MiddlewarePipelineInterface;

    /**
     * The final handler that will produce the response
     *
     * @param RequestHandlerInterface $handler
     * @return MiddlewarePipelineInterface
     */
    public function to(RequestHandlerInterface $handler): MiddlewarePipelineInterface;

    /**
     * Returns the produced response
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
