<?php

require __DIR__ . '/../vendor/autoload.php';

/** @var \Quill\Quill $app */
$app = require __DIR__ . '/../boot/boot.php';

$app->group('api', function ($route) {
    $route->get('', fn ($req, $res) => $res->plain(memory_get_peak_usage() / 1024 / 1024 ))->middleware('example');
});

$app->up();
