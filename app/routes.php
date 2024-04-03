<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use App\Middlewares\ApplicationJsonMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->group('/api', function (RouteCollectorProxy $groupApi) {
        $groupApi->group('/users', function (RouteCollectorProxy $groupUsers) {
            $groupUsers->get('', [UserController::class, 'getList']);
            $groupUsers->get('/{id}', [UserController::class, 'getById']);
            $groupUsers->post('', [UserController::class, 'create']);
            $groupUsers->put('/{id}', [UserController::class, 'update']);
            $groupUsers->delete('/{id}', [UserController::class, 'delete']);
        });

        $groupApi->group('/products', function (RouteCollectorProxy $groupProducts) {
            $groupProducts->get('', [ProductController::class, 'getList']);
            $groupProducts->get('/{id}', [ProductController::class, 'getById']);
            $groupProducts->post('', [ProductController::class, 'create']);
            $groupProducts->put('/{id}', [ProductController::class, 'update']);
            $groupProducts->delete('/{id}', [ProductController::class, 'delete']);
        });

        $groupApi->group('/auth', function (RouteCollectorProxy $groupAuth) {
            $groupAuth->get('', [AuthController::class, 'init']);
            $groupAuth->post('/login', [AuthController::class, 'login']);
            // $groupAuth->post('/logout', [AuthController::class, 'logout']);
            // $groupAuth->post('/refresh', [AuthController::class, 'refresh']);
            // $groupAuth->post('/me', [AuthController::class, 'me']);
        });
    })->add(ApplicationJsonMiddleware::class);
};
