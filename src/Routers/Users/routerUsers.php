<?php

use App\Http\Controllers\Api\V1\Users\UsuarioController;

$userController = $container->get(UsuarioController::class);

$router->create('GET', '/api/v1/users', [$userController, 'index'], $authService);
$router->create('GET', '/api/v1/list-users', [$userController, 'indexWithoutPagination'], $authService);
$router->create('POST', '/api/v1/users', [$userController, 'store'], $authService);
$router->create('PUT', '/api/v1/users/{id}', [$userController, 'update'], $authService);
$router->create('DELETE', '/api/v1/users/{id}', [$userController, 'destroy'], $authService);
