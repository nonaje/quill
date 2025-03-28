<?php

use Quill\Contracts\Router\RouterInterface;

return function(RouterInterface $route) {
    $route->get('/:id', function ($req, $res, $params) {
        return $res->json([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'id' => $params['id'],
        ]);
    });
};
