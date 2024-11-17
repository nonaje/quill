<?php

declare(strict_types=1);

namespace Quill\Lifecycle;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Quill\Enums\RequestAttribute;
use Throwable;

final readonly class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestHandlerInterface $errorHandler) { }

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
