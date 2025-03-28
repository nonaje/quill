<?php

return function (\Quill\Contracts\Container\ContainerInterface $container): void {
    /**
     * --------------------------------------------------
     * Load configuration files into memory
     * --------------------------------------------------
     */
    $configurationFiles = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config';
    new \Quill\Loaders\ConfigurationFilesLoader($container)->load($configurationFiles);

    /**
     * --------------------------------------------------
     * Load configuration files into memory
     * --------------------------------------------------
     */
    new \Quill\Loaders\DotEnvLoader($container)->load();

    /**
     * --------------------------------------------------
     * Load routes files into memory
     * --------------------------------------------------
     */
    $routeFies = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'routes';
    new \Quill\Loaders\RouteFilesLoader($container)->load($routeFies);
};
