<?php

use App\Http\Controllers\Api\V1\Users\UsuarioController;

$userController = $container->get(UsuarioController::class);

$router->create('GET', '/api/v1/usuarios', [$userController, 'index'], $authService);
