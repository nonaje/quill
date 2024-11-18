<?php

declare(strict_types=1);

namespace Quill\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Quill\Enums\RequestAttribute;
use Throwable;

final readonly class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestHandlerInterface $errorHandler) { }

    /**
     * Processes an incoming server request and returns a response, handling exceptions.
     *
     * This method allows the middleware to pass the request to the next handler in the chain.
     * If an exception is thrown during the request handling, it catches the exception and
     * forwards the request to the configured error handler, adding the exception as an attribute
     * to the request for further processing.
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @param RequestHandlerInterface $handler The request handler to process the request.
     * @return ResponseInterface The generated response after handling the request or the error.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->errorHandler->handle(
                request: $request->withAttribute(RequestAttribute::ERROR->value, $e)
            );
        }
    }
}
