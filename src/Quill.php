<?php

declare(strict_types=1);

namespace Quill;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Quill\Config\Config;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\Handler\ErrorHandlerInterface;
use Quill\Contracts\Handler\RequestHandlerChainInterface;
use Quill\Contracts\Response\ResponseSenderInterface;
use Quill\Contracts\Router\MiddlewareStoreInterface;
use Quill\Contracts\Router\RouteStoreInterface;
use Quill\Exceptions\FileNotFoundException;
use Quill\Factory\Psr7\Psr7Factory;
use Quill\Factory\QuillResponseFactory;
use Quill\Links\ExecuteRouteMiddlewares;
use Quill\Links\ExecuteRouteTarget;
use Quill\Links\HandleFutureErrors;
use Quill\Links\IdentifySearchedRoute;
use Quill\Links\ResolveRouteParameters;
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
        private readonly MiddlewareStoreInterface     $appMiddlewares,
        private readonly RequestHandlerChainInterface $stack,
        private readonly ResponseSenderInterface      $response,
        private ErrorHandlerInterface                 $errorHandler,

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
        set_error_handler([$errorHandler, 'captureError']);
        set_exception_handler([$errorHandler, 'captureException']);

        $this->errorHandler = $errorHandler;

        return $this;
    }

    /** @inheritDoc */
    public function up(): void
    {
        $this->prepareLifecycle();

        $request = Psr7Factory::createPsr7ServerRequest();

        $response = $this->handleRequest($request);

        $this->response->send(QuillResponseFactory::createFromPsrResponse($response));
    }

    /**
     * It merges user-registered global middlewares with application lifecycle.
     *
     * Lifecycle:
     * 1.- Set the error handler
     * 2.- Identifies the searched route or returns 404
     * 3.- Try to resolve the route parameters if applicable
     * 4.- Run the global middlewares registered by the user
     * 5.- Run the route middlewares
     *
     * @return void
     */
    private function prepareLifecycle(): void
    {
        // Quill middlewares (Required Lifecycle)
        $lifecycle = [
            new HandleFutureErrors($this->errorHandler),
            new IdentifySearchedRoute($this),
            new ResolveRouteParameters,
            3 => new ExecuteRouteMiddlewares
        ];

        // Sets the order of the user-defined global middlewares just before the route middlewares.
        array_splice($lifecycle, 3, 0, $this->appMiddlewares->all());

        // Transform multidimensional arrays into one-dimensional array
        $stack = array_flatten($lifecycle);

        // It is necessary to reverse the array to comply with the LIFO concept
        $stack = array_reverse($stack);

        // First element to be set but last to run (LIFO)
        $this->stack->setLast(new ExecuteRouteTarget);

        // Consequent links in the chain, the last element added is the first to be executed.
        foreach ($stack as $link) {
            $this->stack->stack($link);
        }
    }

    /**
     * It executes the first element of the stack (Error Handler) which causes the chain execution of the other handlers.
     *
     * It ends by obtaining the response returned by the route target.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    private function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        // Get the las element added to the stack (ErrorHandler)
        $handler = $this->stack->getLast();

        // Run the error handler and start the stack execution chain
        return $handler->handle($request);
    }

    /**
     * Set essential settings for the operation of the application
     *
     * @throws FileNotFoundException
     */
    private function boot(): void
    {
        // Override PHP's default error handler
        $this->setErrorHandler($this->errorHandler);

        // Set the root directory of the application
        Path::setApplicationPath(dirname(__DIR__, 4));

        /*
            Autoload:
               - App config files
               - App environment file (.env)
               - App routes files
        */
        $config = Config::make();
        ConfigurationFilesLoader::make($config)->load();
        DotEnvLoader::make($config)->load();
        RouteFilesLoader::make($this)->load();
    }
}
