<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Config\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $body = $request->getBodyParams() ?? $request->getJsonBody();
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        $result = $this->authService->authenticate($email, $password);

        if (is_null($result)) {
            return json_response(['error' => 'Invalid credentials'], 401);
        }

        $isSecure = true; // HTTPS local e produção
        $sameSite = 'None'; // necessário para cookies cross-site (React em outro domínio/porta)

        setcookie('ART', $result['refresh_token'], [
            'expires' => time() + (60 * 60 * 24 * 7),
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite
        ]);

        unset($result['refresh_token']);

        return json_response($result, 200);
    }

    public function refreshToken()
    {
        $result = $this->authService->prepareRefreshToken();

        if (is_null($result)) {
            return;
        }

        $isSecure = true;
        $sameSite = 'None';

        setcookie('ART', $result['refresh_token'], [
            'expires' => time() + (60 * 60 * 24 * 7),
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite
        ]);

        unset($result['refresh_token']);

        return json_response($result, 200);
    }

    public function logout(Request $request)
    {
        $token = $request->getAuthorization();
        setcookie('ART', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ]);

        return json_response(['message' => 'Logged out successfully'], 200);
    }
}
