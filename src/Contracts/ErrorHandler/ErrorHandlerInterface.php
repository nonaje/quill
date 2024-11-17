<?php

declare(strict_types=1);

namespace Quill\Contracts\ErrorHandler;

use ErrorException;

interface ErrorHandlerInterface
{
    /**
     * Handles general errors within the application.
     *
     * @throws ErrorException
     *
     * @param int $code
     * @param string $message
     * @param string|null $file
     * @param int|null $line
     * @return void
     */
    public function handleError(int $code, string $message, ?string $file = null, ?int $line = null): void;
}
