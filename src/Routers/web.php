<?php

use App\Config\Auth;
use App\Config\Router;
use App\Http\Controllers\Api\V1\Auth\AuthController;

require_once __DIR__ . '/../../vendor/autoload.php';



$router = new Router();
$auth = new Auth();
$authController = new AuthController();

$router->create('GET', '/api/v1/auth/login/{id}/{token}', [$authController, 'login'], null);

return $router;
