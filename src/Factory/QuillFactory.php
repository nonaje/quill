<?php

declare(strict_types=1);

namespace Quill\Factory;

use Quill\Config\Config;
use Quill\Handler\JsonErrorHandler;
use Quill\Handler\RequestHandlerStack;
use Quill\Loaders\ConfigurationFilesLoader;
use Quill\Loaders\DotEnvLoader;
use Quill\Loaders\RouteFilesLoader;
use Quill\Quill;
use Quill\Response\ResponseMessenger;
use Quill\Router\MiddlewareStore;
use Quill\Router\RouteStore;
use Quill\Support\Dot\Parser;

final class QuillFactory
{
    public static function make(): Quill
    {
        return Quill::make(
            routeMiddlewares: new MiddlewareStore,
            globalMiddlewares: new MiddlewareStore,
            routeStore: new RouteStore,
            errorHandler: JsonErrorHandler::make(),
            stack: RequestHandlerStack::make(),
            messenger: ResponseMessenger::make(),
        );
    }
}
