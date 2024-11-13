<?php

namespace Quill\Handler;

use Quill\Contracts\Handler\ErrorHandlerInterface;
use Quill\Enums\Http\HttpCode;
use Quill\Factory\QuillResponseFactory;
use Quill\Response\ResponseMessenger;
use Quill\Support\Traits\Singleton;
use Throwable;

class JsonErrorHandler implements ErrorHandlerInterface
{
    use Singleton;

    public function captureException(Throwable $e): never
    {
        $response = QuillResponseFactory::createQuillResponse()
            ->code(HttpCode::SERVER_ERROR)
            ->json([
                'success' => false,
                'code' => $e->getCode() ?: HttpCode::SERVER_ERROR,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ]);

        ResponseMessenger::make()->send($response);
    }

    public function captureError(
        int $errorCode,
        string $errorDescription,
        string $filename = null,
        int $line = null,
        array $context = null
    ): never {
        $response = QuillResponseFactory::createQuillResponse()
            ->code(HttpCode::SERVER_ERROR)
            ->json([
                'code' => $errorCode,
                'description' => $errorDescription,
                'filename' => $filename,
                'line' => $line,
                'context' => $context,
            ]);

        ResponseMessenger::make()->send($response);
    }
}
