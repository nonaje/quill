<?php

declare(strict_types=1);

namespace Quill\Handler\Error;

use Quill\Enums\Http\HttpCode;
use Quill\Contracts\Response\ResponseInterface;

class JsonErrorHandler extends ErrorHandler
{
    /** @ineritDoc */
    protected function toResponse(): ResponseInterface
    {
        $e = $this->getError();

        return $this->response
            ->code(HttpCode::tryFrom($e->getCode()) ?? HttpCode::SERVER_ERROR)
            ->json([
                'success' => false,
                'code' => $e->getCode() ?: HttpCode::SERVER_ERROR,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ]);
    }
}
