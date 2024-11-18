<?php

declare(strict_types=1);

namespace Quill\Handler\Error;

use ErrorException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Contracts\ErrorHandler\ErrorHandlerInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Enums\RequestAttribute;
use Quill\Support\Singleton;
use Throwable;

abstract class ErrorHandler implements RequestHandlerInterface, ErrorHandlerInterface
{
    use Singleton;

    protected function __construct(protected ResponseInterface $response) { }

    /**
     * The request instance that contains the error
     *
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /** @ineritDoc  */
    protected abstract function toResponse(): ResponseInterface;

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): PsrResponseInterface
    {
        $this->request = $request;

        return $this->toResponse()->getPsrResponse();
    }

    /**
     * Convert a PHP generic error into an ErrorException and throw it to be caught by the ExceptionHandlingMiddleware
     *
     * @throws ErrorException
     */
    public function handleError(int $code, string $message, string $file = null, int $line = null, array $context = null): void
    {
        throw new ErrorException(
            message: $message,
            code: $code,
            filename: $file,
            line: $line,
        );
    }

    /**
     * Gets and returns the error from the request
     *
     * @return Throwable
     */
    protected function getError(): Throwable
    {
        return $this->request->getAttribute(RequestAttribute::ERROR->value);
    }
}
