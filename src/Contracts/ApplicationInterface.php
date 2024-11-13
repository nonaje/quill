<?php

declare(strict_types=1);

namespace Quill\Contracts;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Contracts\Handler\ErrorHandlerInterface;
use Quill\Contracts\Router\RouterInterface;

/** @mixin RouterInterface */
interface ApplicationInterface
{
    /**
     * Use the specified error handler
     *
     * @param ErrorHandlerInterface $errorHandler
     * @return ApplicationInterface
     */
    public function setErrorHandler(ErrorHandlerInterface $errorHandler): ApplicationInterface;

    /**
     * Register a new global middleware
     *
     * @param string|array|Closure|MiddlewareInterface $middleware
     * @return ApplicationInterface
     */
    public function use(string|array|Closure|MiddlewareInterface $middleware): ApplicationInterface;

    /**
     * This function should be called after registering the global middlewares and routes.
     *
     * Starts the application by sending the request through all the middlewares and returning the response to the client.
     *
     * @return void
     */
    public function up(): void;
}
