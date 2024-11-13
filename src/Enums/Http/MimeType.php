<?php

declare(strict_types=1);

namespace Quill\Enums\Http;

use Quill\Enums\Steroids;

enum MimeType: string
{
    use Steroids;

    case PLAINTEXT = 'text/plain';
    case JSON = 'application/json';
}
