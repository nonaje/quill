<?php

declare(strict_types=1);

namespace Quill;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\Middleware\MiddlewarePipelineInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Contracts\Response\ResponseSenderInterface;
use Quill\Contracts\Router\MiddlewareStoreInterface;
use Quill\Contracts\Router\RouteStoreInterface;
use Quill\Handler\RequestHandler;
use Quill\Middleware\ExceptionHandlingMiddleware;
use Quill\Middleware\ExecuteRouteMiddlewares;
use Quill\Middleware\FindRouteMiddleware;
use Quill\Response\Response;
use Quill\Router\Router;
use Throwable;

final class Quill extends Router implements ApplicationInterface
{
    /**
     * @throws Throwable
     */
    public function __construct(
        // Quill properties
        private RequestHandlerInterface $errorHandler,
        private readonly MiddlewareStoreInterface $appMiddlewares,
        private readonly MiddlewarePipelineInterface $middlewarePipeline,
        private readonly ResponseSenderInterface $response,

        // Router properties
        RouteStoreInterface $routeStore,
    ) {
        parent::__construct($routeStore);

        $this->boot();
    }

    /**
     * Set essential settings for the operation of the application
     *
     * @throws Throwable
     */
    private function boot(): void
    {
    }

    /** @inheritDoc */
    public function use(string|array|Closure|MiddlewareInterface $middleware): ApplicationInterface
    {
        $this->appMiddlewares->add($middleware);

        return $this;
    }

    /** @inheritDoc */
    public function setErrorHandler(RequestHandlerInterface $errorHandler): ApplicationInterface
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /** @inheritDoc
     *
     * @throws ContainerExceptionInterface
     */
    public function up(): void
    {
        $request = resolve(RequestInterface::class)->getPsrRequest();

        $response = $this->middlewarePipeline
            ->send($request)
            ->through([
                new ExceptionHandlingMiddleware($this->errorHandler),
                new FindRouteMiddleware($this),
                // Run user-defined global middlewares before the route middlewares.
                ...$this->appMiddlewares->all(),
                new ExecuteRouteMiddlewares(
                    new $this->middlewarePipeline()
                ),
            ])
            ->to(new RequestHandler())
            ->getResponse();

        $response = refresh(id: ResponseInterface::class, refreshed: fn() => new Response($response));

        $this->response->send($response);
    }
}
