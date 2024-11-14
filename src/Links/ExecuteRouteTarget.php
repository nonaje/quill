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
    private RouteInterface          $route;

    public function handle(ServerRequestInterface $request): PsrResponseInterface
    {
        $this->route = $request->getAttribute('route');
        $this->response = QuillResponseFactory::createQuillResponse();
        $this->request = QuillRequestFactory::createFromPsrRequest($request)
            ->setMatchedRoute($this->route);

        return $this->determineRouteTarget();
    }

    private function determineRouteTarget(): PsrResponseInterface {
        return match (true) {
            is_string($this->route->target()) => $this->resolveStringTarget(),
            is_array($this->route->target()) => $this->resolveArrayTarget(),
            is_callable($this->route->target()) => $this->resolveCallableTarget(),
            default => throw new LogicException('It is not possible to determine the target of the route'),
        };
    }

    private function resolveStringTarget(): PsrResponseInterface
    {
        $toResolve = explode('@', $this->route->target());
        $controller = $toResolve[0];
        $method = $toResolve[1] ?? '__invoke';

        /** @var ResponseInterface $final */
        $final = (new $controller($this->request, $this->response, ...$this->route->params()))->{$method}();

        return $final->getPsrResponse();
    }

    private function resolveArrayTarget(): PsrResponseInterface
    {
        $controller = $this->route->target()[0];
        $method = $target[1] ?? '__invoke';

        /** @var ResponseInterface $final */
        $final = (new $controller($this->request, $this->response, ...$this->route->params()))->{$method}();

        return $final->getPsrResponse();
    }

    private function resolveCallableTarget(): PsrResponseInterface
    {
        /** @var ResponseInterface $final */
        $final = ($this->route->target())($this->request, $this->response, ...$this->route->params());

        return $final->getPsrResponse();
    }
}
