<?php

declare(strict_types=1);

namespace Quill;

use Exception;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Config\Config;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\Middleware\MiddlewarePipelineInterface;
use Quill\Contracts\Response\ResponseSenderInterface;
use Quill\Contracts\Router\MiddlewareStoreInterface;
use Quill\Contracts\Router\RouteStoreInterface;
use Quill\Factory\Psr7\Psr7Factory;
use Quill\Factory\QuillResponseFactory;
use Quill\Middleware\ExecuteRouteMiddlewares;
use Quill\Handler\RequestHandler;
use Quill\Middleware\ExceptionHandlingMiddleware;
use Quill\Middleware\FindRouteMiddleware;
use Quill\Loaders\ConfigurationFilesLoader;
use Quill\Loaders\DotEnvLoader;
use Quill\Loaders\RouteFilesLoader;
use Quill\Router\Router;
use Quill\Support\Path;
use Quill\Support\Singleton;

final class Quill extends Router implements ApplicationInterface
{
    use Singleton;

    /**
     * @throws Exception
     */
    protected function __construct(
        // Quill properties
        private RequestHandlerInterface              $errorHandler,
        private readonly MiddlewareStoreInterface    $appMiddlewares,
        private readonly MiddlewarePipelineInterface $middlewarePipeline,
        private readonly ResponseSenderInterface     $response,

        // Router properties
        RouteStoreInterface                          $routeStore,
    ) {
        parent::__construct($routeStore);

        $this->boot();
    }

    /** @inheritDoc */
    public function use(string|array|\Closure|MiddlewareInterface $middleware): ApplicationInterface
    {
        $this->appMiddlewares->add($middleware);

        return $this;
    }

    /** @inheritDoc */
    public function setErrorHandler(RequestHandlerInterface $errorHandler): ApplicationInterface
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /** @inheritDoc */
    public function up(): void
    {
        $request = Psr7Factory::createPsr7ServerRequest();

        $response = $this->middlewarePipeline
            ->send($request)
            ->through([
                new ExceptionHandlingMiddleware($this->errorHandler),
                new FindRouteMiddleware($this),
                // Run user-defined global middlewares before the route middlewares.
                ...$this->appMiddlewares->all(),
                new ExecuteRouteMiddlewares(
                    new $this->middlewarePipeline
                ),
            ])
            ->to(new RequestHandler)
            ->getResponse();

        $this->response->send(QuillResponseFactory::createFromPsrResponse($response));
    }

    /**
     * Set essential settings for the operation of the application
     *
     * @throws Exception
     */
    private function boot(): void
    {
        // Override PHP's default error handler
        set_error_handler([$this->errorHandler, 'handleError']);

        // Set the root directory of the application
        Path::setApplicationPath(dirname(__DIR__, 4));

        /*----------------------------------------------------------------------------
        | AUTOLOAD                                                                    |
        |-----------------------------------------------------------------------------
        | - App config files                                                          |
        | - App environment file (.env)                                               |
        | - App routes files                                                          |
        |----------------------------------------------------------------------------*/
        $config = Config::make();
        ConfigurationFilesLoader::make($config)->load();
        DotEnvLoader::make($config)->load();
        RouteFilesLoader::make($this)->load();
    }
}
