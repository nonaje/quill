<?php

declare(strict_types=1);

namespace Quill\Response;

use Exception;
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
    public function plain(string $plain): ResponseInterface
    {
        return $this->setPsrResponse(
            $this->body($plain, MimeType::PLAIN_TEXT)
        );
    }

    /** @inheritDoc */
    public function json(array $data): ResponseInterface
    {
        return $this->setPsrResponse(
            $this->body(json_encode($data), MimeType::JSON)
        );
    }

    /** @inheritDoc */
    public function html(string $html): ResponseInterface
    {
        return $this->setPsrResponse(
            $this->body($html, MimeType::HTML)
        );
    }

    /**
     * @throws Exception
     */
    public function view(string $view): ResponseInterface
    {
        //Checks if the received argument is the name of a html file inside the 'views' folder
        if (file_exists($path = $view) ||
            file_exists($path = Path::toFile($view)) ||
            file_exists($path = Path::toFile($view . '.html'))
        ) {
            $html = file_get_contents($path);
            return $this->html($html);
        }

        throw new Exception('The view "' . $view . '" does not exist or is not readable.');
    }

    /** @inheritDoc */
    public function code(HttpCode $code): ResponseInterface
    {
        $response = $this->psrResponse->withStatus($code->value);

        return $this->setPsrResponse($response);
    }

    /**
     * Set the updated instance of the psr response
     *
     * @param PsrResponseInterface $response
     * @return ResponseInterface
     */
    private function setPsrResponse(PsrResponseInterface $response): ResponseInterface
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
