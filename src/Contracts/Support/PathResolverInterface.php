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

    /**
     * Returns the absolute path to a file inside the "config" folder in the root path of the application.
     *
     * @param string $filename
     * @return string
     */
    public static function toConfig(string $filename): string;

    /**
     * Returns the absolute path to a file inside the "routes" folder in the root path of the application.
     *
     * @param string $filename
     * @return string
     */
    public static function toRoute(string $filename): string;

    /**
     * Returns the absolute path to a file inside the "views" folder in the root path of the application.
     *
     * @param string $filename
     * @return string
     */
    public static function toHtml(string $filename): string;
}
