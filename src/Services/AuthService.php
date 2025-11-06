<?php

namespace App\Services;

use App\Config\Auth;
use App\Models\Users\Usuario;
use App\Repositories\Entities\Users\UsuarioRepository;

class AuthService extends Auth
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function authenticate(string $username, string $password)
    {
        $user = $this->usuarioRepository->login($username);
        if (!is_null($user)) {
            if (!password_verify($password, $user->senha)) {
                return null;
            }

            return $this->prepareToken($user);
        }
        return null;
    }

    public function prepareToken(Usuario $user)
    {
        $tokens = $this->generateTokens($user);
        unset($user->senha);
        return [
            'user' => $user,
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ];
    }

    public function prepareRefreshToken()
    {
        if (!isset($_COOKIE['ART'])) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'No refresh token provided']);
            return;
        }

        $refreshToken = $_COOKIE['ART'];

        if ($this->isValidToken($refreshToken)) {
            $userUuid = $this->getUserIdFromToken($refreshToken);
            if ($userUuid) {
                $user = $this->usuarioRepository->findByUuid($userUuid);
                if (!is_null($user)) {
                    return $this->prepareToken($user);
                }
            }
        }
        return null;
    }

    public function logout(string $token)
    {
        if ($this->isValidToken($token)) {
            $this->invalidateToken($token);
            return true;
        }
        return false;
    }
}
