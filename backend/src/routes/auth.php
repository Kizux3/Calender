<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UserController;

return function ($app) {
    $app->group('/api/auth', function (RouteCollectorProxy $group) {
        $group->post('/register', [UserController::class, 'register']);
        $group->post('/login', [UserController::class, 'login']);
    });
}; 