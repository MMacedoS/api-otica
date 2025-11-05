<?php

namespace App\Config;

use App\Config\Cors;

Cors::handle([
    $_ENV['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost',
]);

class Router
{
    protected $routes = [];
    protected $auth = null;
    protected $request = null;
    public $userLogged = null;

    public function create(string $method, string $path, callable $handler, ?Auth $requiresAuth): void
    {
        $normalizedPath = rtrim(parse_url($path, PHP_URL_PATH), '/');
        $this->routes[$method][$normalizedPath] = [
            'handler' => $handler,
            'auth' => $requiresAuth,
        ];
    }

    public function init(): void
    {
        $this->request = new Request();
        $method = $this->request->getMethod();
        $uri = rtrim(parse_url($this->request->getUri(), PHP_URL_PATH), '/');

        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            $handler = $route['handler'];
            $requiresAuth = $route['auth'];

            if ($requiresAuth !== null) {
                $authHeader = $this->request->getHeaders()['Authorization'] ?? null;
                $token = null;

                if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                    $token = $matches[1];
                }

                if (!$requiresAuth->getTokenExpiry($token)) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    return;
                }

                // Aqui você pode decodificar o token e definir o usuário logado
                // $this->userLogged = ...;
            }

            call_user_func($handler, $this->request, $this->userLogged);
            return;
        }
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
