<?php

declare(strict_types=1);

namespace Quill\Exceptions\Http;

use Quill\Enums\Http\HttpCode;

class RouteNotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            message: 'The specified route does not exists',
            code: HttpCode::NOT_FOUND->value
        );
    }
}
