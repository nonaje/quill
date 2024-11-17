<?php

declare(strict_types=1);

namespace Quill;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quill\Config\Config;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\ErrorHandler\ErrorHandlerInterface;
use Quill\Contracts\Lifecycle\LifecyclePipelineInterface;
use Quill\Contracts\Response\ResponseSenderInterface;
use Quill\Contracts\Router\MiddlewareStoreInterface;
use Quill\Contracts\Router\RouteStoreInterface;
use Quill\Exceptions\FileNotFoundException;
use Quill\Factory\Psr7\Psr7Factory;
use Quill\Factory\QuillResponseFactory;
use Quill\Lifecycle\ExecuteRouteMiddlewares;
use Quill\Lifecycle\RequestHandler;
use Quill\Lifecycle\ExceptionHandlingMiddleware;
use Quill\Lifecycle\SearchRouteMiddleware;
use Quill\Lifecycle\RouteParametersMiddleware;
use Quill\Loaders\ConfigurationFilesLoader;
use Quill\Loaders\DotEnvLoader;
use Quill\Loaders\RouteFilesLoader;
use Quill\Router\Router;
use Quill\Support\PathFinder\Path;
use Quill\Support\Traits\Singleton;

final class Quill extends Router implements ApplicationInterface
{
    use Singleton;

    /**
     * @throws FileNotFoundException
     */
    protected function __construct(
        // Quill properties
        private RequestHandlerInterface $errorHandler,
        private readonly MiddlewareStoreInterface $appMiddlewares,
        private readonly LifecyclePipelineInterface $handler,
        private readonly ResponseSenderInterface $response,

        // Router properties
        RouteStoreInterface                           $routeStore,
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
    public function setErrorHandler(ErrorHandlerInterface $errorHandler): ApplicationInterface
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /** @inheritDoc */
    public function up(): void
    {
        $request = Psr7Factory::createPsr7ServerRequest();

        $response = $this->handler
            ->send($request)
            ->through([
                new ExceptionHandlingMiddleware($this->errorHandler),
                new SearchRouteMiddleware($this),
                new RouteParametersMiddleware,
                // Run user-defined global middlewares before the route middlewares.
                ...$this->appMiddlewares->all(),
                new ExecuteRouteMiddlewares,
            ])
            ->to(new RequestHandler)
            ->getResponse();

        $this->response->send(QuillResponseFactory::createFromPsrResponse($response));
    }

    /**
     * Set essential settings for the operation of the application
     *
     * @throws FileNotFoundException
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
