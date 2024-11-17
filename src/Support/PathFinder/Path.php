<?php

declare(strict_types=1);

namespace Quill\Support\PathFinder;

use Exception;
use Quill\Contracts\Support\PathResolverInterface;
use Quill\Enums\Http\HttpCode;

class Path implements PathResolverInterface
{
    protected static string $appPath;

    /**
     * @throws Exception
     */
    public static function setApplicationPath(string $path): void
    {
        if (! file_exists($path)) {
            throw new Exception('The specified application path does not exist', HttpCode::SERVER_ERROR->value);
        }

        static::$appPath = $path;
    }

    public static function toConfig(string $filename = ''): string
    {
        return self::toFile('config') . self::normalizeFilename($filename);
    }

    public static function toFile(string $filename = ''): string
    {
        return static::$appPath . self::normalizeFilename($filename);
    }

    public static function toRoute(string $filename = ''): string
    {
        return self::toFile('routes') . self::normalizeFilename($filename);
    }

    public static function toHtml(string $filename = ''): string
    {
        if (! str_ends_with($filename, '.html')) {
            $filename .= '.html';
        }

        return self::toFile('views') . self::normalizeFilename($filename);
    }

    protected static function normalizeFilename(string $filename): string
    {
        $filename = trim($filename, '/');

        return "/$filename";
    }
}
