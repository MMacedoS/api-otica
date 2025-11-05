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
        $normalizedPath = $this->normalizePath($path);
        $this->routes[$method][$normalizedPath] = [
            'callback' => $handler,
            'auth' => $requiresAuth,
        ];
    }

    public function init()
    {
        $httpMethod = $_SERVER["REQUEST_METHOD"];
        $requestUri = $_SERVER["REQUEST_URI"];
        $request = new Request();

        $normalizedRequestUri = $this->normalizePath($requestUri);

        // Verifica se a rota existe
        foreach ($this->routes[$httpMethod] as $path => $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
            $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

            if (preg_match($pattern, $normalizedRequestUri, $matches)) {
                array_shift($matches); // Remove o caminho completo
                $params = $matches;

                $token = $request->getAuthorization();

                // Verifica autenticação
                if (!is_null($route['auth']) && !$route['auth']->isValidToken($token)) {
                    http_response_code(401);
                    echo json_encode([
                        'status' => 401,
                        'message' => 'Unauthorized'
                    ]);
                    return;
                }

                // Executa o callback da rota
                return call_user_func_array($route['callback'], array_merge([$request], $params));
            }
        }
    }

    private function normalizePath($path)
    {
        return rtrim(parse_url($path, PHP_URL_PATH), '/');
    }
}
