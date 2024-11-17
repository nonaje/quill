<?php

declare(strict_types=1);

namespace Quill\Lifecycle;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Contracts\Router\RouteInterface;
use Quill\Contracts\Router\RouterInterface;
use Quill\Enums\Http\HttpCode;
use Quill\Enums\RequestAttribute;
use Psr\Http\Message\ResponseInterface;
use Quill\Router\Route;

final readonly class FindRouteMiddleware implements MiddlewareInterface
{
    public function __construct(private RouterInterface $router) { }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle(
            request: $request->withAttribute(RequestAttribute::ROUTE->value, $this->find($request))
        );
    }

    /**
     * @throws Exception
     */
    private function find(ServerRequestInterface $request): RouteInterface
    {
        foreach ($this->router->routes() as $route) {
            [$match, $params] = $this->resolveRoute($route, $request);

            if (! $match) continue;

            // Once the route is found, all registered routes are deleted to free up memory
            $this->router->clear();

            return new Route(
                uri: $route->uri(),
                method: $route->method(),
                target: $route->target(),
                middlewares: $route->getMiddlewares(),
                params: $params
            );
        }

        throw new Exception('The specified route does not exists', HttpCode::NOT_FOUND->value);
    }

    private function resolveRoute(RouteInterface $route, ServerRequestInterface $request): array
    {
        // If the request method does not match the route method, then the route does not match
        if ($route->method()->value !== strtoupper($request->getMethod())) {
            return [false, []];
        }

        $routeParts = array_values(array_filter(explode('/', $route->uri())));
        $wantedParts = array_values(array_filter(explode('/', $request->getUri()->getPath())));

        // If the segment quantities do not match, then the route does not match
        if (count($routeParts) !== count($wantedParts)) {
            return [false, []];
        }

        // Check if route is exactly matched
        if (count(array_diff($routeParts, $wantedParts)) === 0) {
            return [true, []];
        }

        // Check if route can match based on parameters
        $replacedRegisteredUriWithParameters = [];
        $params = [];
        foreach ($routeParts as $key => $part) {
            $replacedRegisteredUriWithParameters[$key] = $part;
            $wantedPart = $wantedParts[$key];

            if (str_starts_with($part, ':') && isset($wantedPart)) {
                $replacedRegisteredUriWithParameters[$key] = $wantedPart;
                // Delete the ":" character
                $params[substr($part, 1)] = $wantedPart;
            }
        }

        dd($params, $replacedRegisteredUriWithParameters);
        if (count(array_diff($replacedRegisteredUriWithParameters, $wantedParts)) === 0) {
            return [true, $params];
        }

        return [false, []];
    }
}
