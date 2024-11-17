<?php

declare(strict_types=1);

namespace Quill\Contracts\Support;

interface PathResolverInterface
{
    public static function setApplicationPath(string $path): void;

    public static function toConfig(string $filename): string;

    public static function toFile(string $filename): string;

    public static function toRoute(string $filename): string;

    public static function toHtml(string $filename): string;
}
