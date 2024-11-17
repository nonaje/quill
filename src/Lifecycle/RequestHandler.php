<?php

declare(strict_types=1);

namespace Quill\Lifecycle;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Router\RouteInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Enums\RequestAttribute;
use Quill\Factory\QuillRequestFactory;
use Quill\Factory\QuillResponseFactory;
use LogicException;

final readonly class RequestHandler implements RequestHandlerInterface
{
    private ResponseInterface       $response;
    private RequestInterface        $request;
    private RouteInterface          $route;

    public function handle(ServerRequestInterface $request): PsrResponseInterface
    {
        $this->route = $request->getAttribute(RequestAttribute::ROUTE->value);
        $this->response = QuillResponseFactory::createQuillResponse();
        $this->request = QuillRequestFactory::createFromPsrRequest($request);

        /** @var ResponseInterface|null $response */
        $response = ($this->resolveRouteTarget())();

        return is_null($response) ? $this->response->getPsrResponse() : $response->getPsrResponse();
    }

    private function resolveRouteTarget(): callable
    {
        return match (true) {
            is_string($this->route->target()) => $this->resolveStringTarget(),
            is_array($this->route->target()) => $this->resolveArrayTarget(),
            is_callable($this->route->target()) => $this->resolveCallableTarget(),
            default => throw new LogicException('It is not possible to determine the target of the route'),
        };
    }

    private function resolveStringTarget(): callable
    {
        $toResolve = explode('@', $this->route->target());
        $controller = $toResolve[0];
        $method = $toResolve[1] ?? '__invoke';

        return fn ()  => (new $controller($this->request, $this->response, $this->route->params()))->{$method}();
    }

    private function resolveArrayTarget(): callable
    {
        $controller = $this->route->target()[0];
        $method = $target[1] ?? '__invoke';

        return fn ()  => (new $controller($this->request, $this->response, $this->route->params()))->{$method}();
    }

    private function resolveCallableTarget(): callable
    {
        return fn () => ($this->route->target())($this->request, $this->response, $this->route->params());
    }
}
