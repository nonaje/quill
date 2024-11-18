<?php

declare(strict_types=1);

namespace Quill\Factory;

use Quill\Handler\Error\JsonErrorHandler;
use Quill\Handler\MiddlewarePipelineHandler;
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
            errorHandler: JsonErrorHandler::make(QuillResponseFactory::createQuillResponse()),
            middlewarePipeline: new MiddlewarePipelineHandler,
            response: ResponseSender::make(),
        );
    }
}
