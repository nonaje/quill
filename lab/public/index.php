<?php

use Quill\Contracts\Request\RequestInterface;
use Quill\Contracts\Response\ResponseInterface;
use Quill\Contracts\Router\RouterInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = quill(dirname(__DIR__));

$app->get('/', fn ($req, $res) => $res->json([
    'status' => 'ok',
]));

$app->group('api', function (RouterInterface $route) {
    $route->group('v1', function (RouterInterface $route) {
        $route->group('users', require routes('api/v1/users.php'));
    });

    $route->group('v2', function (RouterInterface $route) {
        // V2 Api routes...

        $route->group('webhooks', function (RouterInterface $route) {
            $route->get('/', fn (RequestInterface $req, ResponseInterface $res) => $res->plain($req->getPsrRequest()->getUri()));
            // Another group routes inside /api/v2
        });
    });
});
