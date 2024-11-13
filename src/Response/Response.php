<?php

declare(strict_types=1);

namespace Quill\Response;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Enums\Http\HttpCode;
use Quill\Enums\Http\HttpHeader;
use Quill\Enums\Http\MimeType;
use Quill\Factory\Psr7\Psr7Factory;

class Response implements ResponseInterface
{
    public function __construct(
        protected PsrResponseInterface $psrResponse
    ) { }

    public function getPsrResponse(): PsrResponseInterface
    {
        return $this->psrResponse;
    }

    public function plain(string $plain): self
    {
        return $this->setPsrResponse(
            $this->body($plain, MimeType::PLAIN_TEXT)
        );
    }

    public function json(array $data): self
    {
        return $this->setPsrResponse(
            $this->body(json_encode($data), MimeType::JSON)
        );
    }

    public function code(HttpCode $code): self
    {
        $response = $this->psrResponse->withStatus($code->value);

        return $this->setPsrResponse($response);
    }

    private function setPsrResponse(PsrResponseInterface $response): self
    {
        $this->psrResponse = $response;

        return $this;
    }

    private function body(string $content, MimeType $mime): PsrResponseInterface
    {
        return $this->getPsrResponse()
            ->withBody(Psr7Factory::streamFactory()->createStream($content))
            ->withHeader(HttpHeader::CONTENT_TYPE->value, $mime->value);
    }
}
