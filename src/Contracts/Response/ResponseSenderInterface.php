<?php

declare(strict_types=1);

namespace Quill\Contracts\Response;

interface ResponseSenderInterface
{
    public function send(ResponseInterface $response): void;
}
