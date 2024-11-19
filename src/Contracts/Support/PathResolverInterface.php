<?php

declare(strict_types=1);

namespace Quill\Contracts\Support;

use Exception;

interface PathResolverInterface
{
    /**
     * It sets the root application path
     * 
     * @throws Exception
     *
     * @param string $path
     * @return void
     */
    public static function setApplicationPath(string $path): void;

    /**
     * Returns the absolute path to a file in the root path of the application.
     *
     * @param string $filename
     * @return string
     */
    public static function toFile(string $filename): string;
}
