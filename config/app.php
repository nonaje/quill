<?php

return [
    'middlewares' => [
        'example' => fn ($req, $next) => $next->handle($req),
    ],
];
