<?php

declare(strict_types=1);

namespace Quill\Contracts\ErrorHandler;

use ErrorException;

interface ErrorHandlerInterface
{
    /**
     * Handles general errors within the application.
     *
     * @param int $code
     * @param string $message
     * @param string|null $file
     * @param int|null $line
     * @return void
     * @throws ErrorException
     *
     */
    public function handleError(int $code, string $message, ?string $file = null, ?int $line = null): void;
}
