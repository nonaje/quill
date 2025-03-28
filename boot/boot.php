<?php

/**
 * --------------------------------------------------
 * Initialize the container instance
 * --------------------------------------------------
 */
$container = \Quill\Container\Container::make();

/**
 * --------------------------------------------------
 * Load constants
 * --------------------------------------------------
 */
(require_once 'constants.php')($container);

/**
 * --------------------------------------------------
 * Register dependencies into container
 * --------------------------------------------------
 */
(require_once 'dependencies.php')($container);

/**
 * --------------------------------------------------
 * Run file loaders
 * --------------------------------------------------
 */
(require_once 'loaders.php')($container);

/**
 * --------------------------------------------------
 * Initialize the Quill Framework instance
 * --------------------------------------------------
 */
$quill = \Quill\Quill::make($container);

/**
 * --------------------------------------------------
 * Start listening for errors
 * --------------------------------------------------
 */
/** @var \Quill\Contracts\ErrorHandler\ErrorHandlerInterface $errorHandler */
$errorHandler = $container->get(\Quill\Contracts\ErrorHandler\ErrorHandlerInterface::class);
$errorHandler->displayErrors = ! $quill->isProduction();
$errorHandler->listen();

return $quill;
