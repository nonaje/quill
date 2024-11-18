<?php

declare(strict_types=1);

namespace Quill\Support;

use Exception;
use Quill\Contracts\Support\PathResolverInterface;
use Quill\Enums\Http\HttpCode;

class Path implements PathResolverInterface
{
    protected static string $appPath;

    /** @ineritDoc  */
    public static function setApplicationPath(string $path): void
    {
        $path = self::normalize($path);

        if (! file_exists($path)) {
            throw new Exception('The specified application path does not exist', HttpCode::SERVER_ERROR->value);
        }

        static::$appPath = $path;
    }

    /** @ineritDoc  */
    public static function toFile(string $filename = ''): string
    {
        return static::$appPath . self::normalize($filename);
    }

    /** @ineritDoc  */
    public static function toConfig(string $filename = ''): string
    {
        return Path::toFile('config') . self::normalize($filename);
    }

    /** @ineritDoc  */
    public static function toRoute(string $filename = ''): string
    {
        return Path::toFile('routes') . self::normalize($filename);
    }

    /** @ineritDoc  */
    public static function toHtml(string $filename = ''): string
    {
        if (! str_ends_with($filename, '.html')) {
            $filename .= '.html';
        }

        return Path::toFile('views') . self::normalize($filename);
    }

    /**
     * Removes the slashes in the file name and adds the slash to the beginning to make sure it is a valid path
     *
     * @param string $filename
     * @return string
     */
    protected static function normalize(string $filename): string
    {
        $filename = trim($filename, '/');

        return "/$filename";
    }
}
