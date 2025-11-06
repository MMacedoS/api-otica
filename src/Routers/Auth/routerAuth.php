<?php

$router->create('POST', '/api/v1/auth', [$authController, 'login'], null);
$router->create('GET', '/api/v1/protected-resource', function () {
    return json_response(['data' => 'This is protected data'], 200);
}, $authService);
$router->create('GET', '/api/v1/refresh', [$authController, 'refreshToken'], null);
$router->create('POST', '/api/v1/logout', [$authController, 'logout'], null);
