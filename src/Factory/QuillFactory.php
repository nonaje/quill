<?php

declare(strict_types=1);

namespace Quill\Factory;

use Quill\ErrorHandler\JsonErrorHandler;
use Quill\LifecyclePipeline;
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
            lifecycle: LifecyclePipeline::make(),
            response: ResponseSender::make(),
        );
    }
}
