<?php

return function (\Quill\Contracts\Container\ContainerInterface $container): void {
    /**
     * --------------------------------------------------
     * Application root directory
     * --------------------------------------------------
     */
    define('APP_ROOT', dirname(__DIR__));

    /**
     * --------------------------------------------------
     * Public directory
     * --------------------------------------------------
     */
    define('PUBLIC_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'public');

    /**
     * --------------------------------------------------
     * Configuration files directory
     * --------------------------------------------------
     */
    define('CONFIG_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'config');

    /**
     * --------------------------------------------------
     * Route files directory
     * --------------------------------------------------
     */
    define('ROUTES_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'routes');
};