<?php

declare(strict_types=1);

namespace Quill\Factory;

use Quill\Handler\JsonErrorHandler;
use Quill\Handler\RequestHandlerStack;
use Quill\Quill;
use Quill\Response\ResponseSender;
use Quill\Router\MiddlewareStore;
use Quill\Router\RouteStore;

final class QuillFactory
{
    public static function make(): Quill
    {
        return Quill::make(
            appMiddlewares: new MiddlewareStore,
            routeStore: new RouteStore,
            errorHandler: JsonErrorHandler::make(),
            stack: RequestHandlerStack::make(),
            response: ResponseSender::make(),
        );
    }
}
