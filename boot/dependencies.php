<?php

declare(strict_types=1);

return function (\Quill\Contracts\Container\ContainerInterface $container): void {
    /**
     * --------------------------------------------------
     * Application Dependencies
     * --------------------------------------------------
     */





    /**
     * --------------------------------------------------
     * Quill Framework Dependencies
     * --------------------------------------------------
     *
     * --------------------------------------------------
     * Singletons
     * --------------------------------------------------
     */
    $container->singleton(
        \Quill\Contracts\Configuration\ConfigurationInterface::class,
        fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Configuration\Config()
    );

    $container->singleton(
        id: \Quill\Contracts\Response\ResponseSenderInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Response\ResponseSender()
    );

    $container->singleton(
        id: \Quill\Contracts\Support\PathResolverInterface::class,
        resolver: fn (\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Support\Path()
    );

    /**
     * --------------------------------------------------
     * Bindings
     * --------------------------------------------------
     */
    $container->register(
        id: \Quill\Contracts\ErrorHandler\ErrorHandlerInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Handler\Error\JsonErrorHandler(
            $container->get(\Quill\Contracts\Response\ResponseInterface::class),
            $container->get(\Quill\Contracts\Response\ResponseSenderInterface::class)
        )
    );

    $container->register(
        \Quill\Contracts\Router\RouteStoreInterface::class,
        fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Router\RouteStore()
    );

    $container->register(
        id: \Quill\Contracts\Middleware\MiddlewarePipelineInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Handler\MiddlewarePipelineHandler()
    );

    $container->register(
        id: \Quill\Contracts\Router\MiddlewareStoreInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Router\MiddlewareStore($container)
    );

    $container->register(
        id: \Psr\Http\Message\RequestInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => \Quill\Factory\Psr7\Psr7Factory::createPsr7ServerRequest()
    );

    $container->register(
        id: \Psr\Http\Message\ResponseInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => \Quill\Factory\Psr7\Psr7Factory::responseFactory()->createResponse()
    );

    $container->register(
        id: \Quill\Contracts\Response\ResponseInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Response\Response($container->get(\Psr\Http\Message\ResponseInterface::class))
    );

    $container->register(
        id: \Quill\Contracts\Request\RequestInterface::class,
        resolver: fn(\Quill\Contracts\Container\ContainerInterface $container) => new \Quill\Request\Request($container->get(\Psr\Http\Message\RequestInterface::class))
    );
};
