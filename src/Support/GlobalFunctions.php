<?php

declare(strict_types=1);

use Quill\Config\Config;
use Quill\Container\Container;
use Quill\Contracts\ApplicationInterface;
use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\QuillBootstrapper;

if (!function_exists('resolve')) {
    function resolve(string $id): mixed
    {
        return Container::make()->get($id);
    }
}

if (!function_exists('refresh')) {
    function refresh(string $id, callable $refreshed): mixed
    {
        return Container::make()->refresh($id, $refreshed);
    }
}

if (!function_exists('quill')) {
    function quill(): ApplicationInterface
    {
        if (!Container::make()->has(ApplicationInterface::class)) {
            (new QuillBootstrapper())->boot();
        }

        return resolve(ApplicationInterface::class);
    }
}

if (!function_exists('request')) {
    function request(): RequestInterface
    {
        return resolve(RequestInterface::class);
    }
}

if (!function_exists('response')) {
    function response(): ResponseInterface
    {
        return resolve(ResponseInterface::class);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return config("env.$key", $default);
    }
}

if (!function_exists('config')) {
    /**
     * @return Config|mixed
     */
    function config(string $key = null, mixed $default = null): mixed
    {
        $config = resolve(ConfigurationInterface::class);

        return $key ? $config->get($key, $default) : $config;
    }
}

if (!function_exists('array_flatten')) {
    function array_flatten(array $toFlatten): array
    {
        $results = [];

        foreach ($toFlatten as $value) {
            if (is_array($value)) {
                $results = array_merge($results, array_flatten($value));
            } else {
                $results[] = $value;
            }
        }

        return array_values($results);
    }
}
