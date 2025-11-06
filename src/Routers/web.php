<?php

use App\Config\Container;
use App\Config\Router;
use App\Services\AuthService;

require_once __DIR__ . '/../../vendor/autoload.php';

$router = new Router();
$container = new Container();
$authService = $container->get(AuthService::class);

$router->create('GET', '/', function () {
    return json_response(['message' => 'API is running'], 200);
}, null);

require_once __DIR__ . '/Auth/routerAuth.php';
require_once __DIR__ . '/Users/routerUsers.php';

return $router;
