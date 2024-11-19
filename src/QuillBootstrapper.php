<?php

namespace Quill;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Quill\Config\Config;
use Quill\Container\Container;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Contracts\Container\ContainerInterface;
use Quill\Contracts\Container\ContainerInterface as CI;
use Quill\Contracts\ErrorHandler\ErrorHandlerInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Factory\Psr7\Psr7Factory;
use Quill\Handler\Error\JsonErrorHandler;
use Quill\Handler\MiddlewarePipelineHandler;
use Quill\Request\Request;
use Quill\Response\Response;
use Quill\Response\ResponseSender;
use Quill\Router\MiddlewareStore;
use Quill\Router\RouteStore;
use Quill\Support\Path;

final class QuillBootstrapper
{
    private ContainerInterface $container;
    /**
     * @throws ContainerExceptionInterface
     */
    public function boot(): void
    {
        $this->container = Container::make();

        $this->registerDependencies();
        $this->registerQuill();
        $this->configureQuill();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function registerDependencies(): void
    {
        // TODO: Replace PSR-7 Factory
        $this->container->singleton(
            id: RequestInterface::class,
            resolver: fn (CI $c) => new Request(Psr7Factory::createPsr7ServerRequest())
        );

        // TODO: Replace PSR-7 Factory
        $this->container->singleton(
            id: ResponseInterface::class,
            resolver: fn (CI $c) => new Response(Psr7Factory::responseFactory()->createResponse())
        );

        $this->container->register(
            id: ErrorHandlerInterface::class,
            resolver: fn (CI $c) => new JsonErrorHandler($c->get(ResponseInterface::class))
        );

        $this->container->singleton(ConfigurationInterface::class, fn (CI $c) => new Config);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    private function configureQuill(): void
    {
        // The "up" function is called automatically when the route registration
        // and general configuration of the application is completed.
        // Normally at the end of the execution of the "index.php" script
        register_shutdown_function(fn () => $this->container->get(ApplicationInterface::class)->up());

        // Override PHP's default error handler
        set_error_handler([$this->container->get(ErrorHandlerInterface::class), 'handleError'], E_ALL);

        // Set the root directory of the application
        Path::setApplicationPath(dirname(__DIR__, 4));

        if (class_exists('Quill\Loaders\DotEnvLoader')) {
            (new ('Quill\Loaders\DotEnvLoader')(
                config: $this->container->get(ConfigurationInterface::class)
            ))->load();
        }

        if (class_exists('Quill\Loaders\ConfigurationFilesLoader')) {
            (new ('Quill\Loaders\ConfigurationFilesLoader')(
                config: $this->container->get(ConfigurationInterface::class)
            ))->load();
        }

        if (class_exists('Quill\Loaders\RouteFilesLoader')) {
            (new ('Quill\Loaders\RouteFilesLoader')(
                router: $this->container->get(ApplicationInterface::class)
            ))->load();
        }
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function registerQuill(): void
    {
        $this->container->singleton(ApplicationInterface::class, fn (CI $c) => new Quill(
            errorHandler: $c->get(ErrorHandlerInterface::class),
            appMiddlewares: new MiddlewareStore,
            middlewarePipeline: new MiddlewarePipelineHandler,
            response: new ResponseSender,
            routeStore: new RouteStore,
        ));
    }
}
