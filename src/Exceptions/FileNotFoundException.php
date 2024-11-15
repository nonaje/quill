<?php

declare(strict_types=1);

namespace Quill\Exceptions;

class FileNotFoundException extends \Exception
{
    public function __construct(string $filename)
    {
        $message = "The specified filename: '$filename' does not exists.";

        parent::__construct($message);
    }
}
