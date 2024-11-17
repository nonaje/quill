<?php

declare(strict_types=1);

namespace Quill\ErrorHandler;

use Quill\Enums\Http\HttpCode;
use Quill\Contracts\Response\ResponseInterface;

class JsonErrorHandler extends ErrorHandler
{
    protected function __construct(protected ResponseInterface $response)
    {
        parent::__construct();
    }

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
