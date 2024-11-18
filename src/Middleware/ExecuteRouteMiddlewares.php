<?php

declare(strict_types=1);

namespace Quill\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Contracts\Middleware\MiddlewarePipelineInterface;
use Quill\Enums\RequestAttribute;

final readonly class ExecuteRouteMiddlewares implements MiddlewareInterface
{
    public function __construct(private MiddlewarePipelineInterface $middlewarePipeline) { }

    /**
     * Processes the middlewares attached to the route
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler to process the request.
     *
     * @return ResponseInterface The response generated after handling the request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middlewares = $request->getAttribute(RequestAttribute::ROUTE->value)->getMiddlewares()->all();

        return $this->middlewarePipeline
            ->send($request)
            ->through($middlewares)
            ->to($handler)
            ->getResponse();
    }
}
