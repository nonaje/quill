<?php

declare(strict_types=1);

namespace Quill\Response;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Enums\Http\HttpCode;
use Quill\Enums\Http\HttpHeader;
use Quill\Enums\Http\MimeType;
use Quill\Factory\Psr7\Psr7Factory;
use Quill\Support\Path;
use Quill\Support\Singleton;

class Response implements ResponseInterface
{
    use Singleton;

    protected function __construct(protected PsrResponseInterface $psrResponse) { }

    /** @inheritDoc */
    public function getPsrResponse(): PsrResponseInterface
    {
        return $this->psrResponse;
    }

    /** @inheritDoc */
    public function plain(string $plain): self
    {
        return $this->setPsrResponse(
            $this->body($plain, MimeType::PLAIN_TEXT)
        );
    }

    /** @inheritDoc */
    public function json(array $data): self
    {
        return $this->setPsrResponse(
            $this->body(json_encode($data), MimeType::JSON)
        );
    }

    /** @inheritDoc */
    public function html(string $html): self
    {
        //Checks if the received argument is the name of an html file inside the 'views' folder
        if (file_exists($path = $html) || file_exists($path = Path::toHtml($html)) || file_exists($path = Path::toHtml($html . '.html'))) {
            $html = file_get_contents($path);
        }

        return $this->setPsrResponse(
            $this->body($html, MimeType::HTML)
        );
    }

    /** @inheritDoc */
    public function code(HttpCode $code): self
    {
        $response = $this->psrResponse->withStatus($code->value);

        return $this->setPsrResponse($response);
    }

    /**
     * Set the updated instance of the psr response
     *
     * @param PsrResponseInterface $response
     * @return self
     */
    private function setPsrResponse(PsrResponseInterface $response): self
    {
        $this->psrResponse = $response;

        return $this;
    }

    /**
     * Return the updated instance of the psr response with the new body and Content-Type header
     *
     * @param string $content
     * @param MimeType $mime
     * @return PsrResponseInterface
     */
    private function body(string $content, MimeType $mime): PsrResponseInterface
    {
        return $this->getPsrResponse()
            ->withBody(Psr7Factory::streamFactory()->createStream($content))
            ->withHeader(HttpHeader::CONTENT_TYPE->value, $mime->value);
    }
}
