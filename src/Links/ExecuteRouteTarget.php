<?php

declare(strict_types=1);

namespace Quill\Links;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Router\RouteInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Factory\QuillRequestFactory;
use Quill\Factory\QuillResponseFactory;
use LogicException;

final readonly class ExecuteRouteTarget implements RequestHandlerInterface
{
    private ResponseInterface       $response;
    private RequestInterface        $request;
    private \Closure|array|string   $target;

    public function handle(ServerRequestInterface $request): PsrResponseInterface
    {
        /** @var RouteInterface $matched */
        $matched = $request->getAttribute('route');
        $this->target = $matched->target();

        $this->response = QuillResponseFactory::createQuillResponse();
        $this->request = QuillRequestFactory::createFromPsrRequest($request)
            ->setMatchedRoute($matched);

        return $this->determineRouteTarget();
    }

    private function determineRouteTarget(): PsrResponseInterface {
        return match (true) {
            is_string($this->target) => $this->resolveStringTarget(),
            is_array($this->target) => $this->resolveArrayTarget(),
            is_callable($this->target) => $this->resolveCallableTarget(),
            default => throw new LogicException('It is not possible to determine the target of the route'),
        };
    }

    private function resolveStringTarget(): PsrResponseInterface
    {
        $toResolve = explode('@', $this->target);
        $controller = $toResolve[0];
        $method = $toResolve[1] ?? '__invoke';

        /** @var ResponseInterface $final */
        $final = (new $controller($this->request, $this->response))->{$method}();

        return $final->getPsrResponse();
    }

    private function resolveArrayTarget(): PsrResponseInterface
    {
        $controller = $this->target[0];
        $method = $target[1] ?? '__invoke';

        /** @var ResponseInterface $final */
        $final = (new $controller($this->request, $this->response))->{$method}();

        return $final->getPsrResponse();
    }

    private function resolveCallableTarget(): PsrResponseInterface
    {
        /** @var ResponseInterface $final */
        $final = ($this->target)($this->request, $this->response);

        return $final->getPsrResponse();
    }
}
