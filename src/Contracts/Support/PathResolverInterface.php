<?php

declare(strict_types=1);

namespace Quill\Contracts\Support;

use Quill\Exceptions\FileNotFoundException;

interface PathResolverInterface
{
    /**
     * @throws FileNotFoundException
     */
    public static function setApplicationPath(string $path): void;

    public static function toConfig(string $filename): string;

    public static function toFile(string $filename): string;

    public static function toRoute(string $filename): string;

    public static function toView(string $filename): string;
}
