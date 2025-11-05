<?php

namespace App\Config;

use App\Utils\LoggerHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private string $secretKey;
    private int $tokenExpiry;

    public function __construct()
    {
        $this->secretKey = $_ENV['AUTH_SECRET_KEY'] ?? 'default_secret_key';
        $this->tokenExpiry = $_ENV['AUTH_TOKEN_EXPIRY'] ?? 3600;
    }

    public function getSecretKey($user): array
    {
        $accessPayload = [
            'iss' => $_ENV['URL_PREFIX_APP'] ?? 'http://localhost',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + $this->tokenExpiry,
        ];

        $refreshPayload = [
            'iss' => $_ENV['URL_PREFIX_APP'] ?? 'http://localhost',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + ($this->tokenExpiry * 2),
        ];

        return [
            'access_token' => JWT::encode($accessPayload, $this->secretKey, 'HS256'),
            'refresh_token' => JWT::encode($refreshPayload, $this->secretKey, 'HS256'),
        ];
    }

    public function getTokenExpiry($token): bool
    {
        if (is_null($token)) {
            return false;
        }
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            if (!isset($decoded->exp) || $decoded->exp < time()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            LoggerHelper::logError("Token validation error: " . $e->getMessage());
            return false;
        }
    }
}
