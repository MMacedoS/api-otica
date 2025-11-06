<?php

namespace App\Config;

use App\Utils\LoggerHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class Auth
{
    public function generateTokens($user): array
    {
        $accessPayload = [
            'iss' => $_ENV['URL_PREFIX_APP'] ?? 'http://localhost',
            'sub' => $user->uuid,
            'iat' => time(),
            'exp' => time() + (int)$_ENV['AUTH_TOKEN_EXPIRY'],
        ];

        $refreshPayload = [
            'iss' => $_ENV['URL_PREFIX_APP'] ?? 'http://localhost',
            'sub' => $user->uuid,
            'iat' => time(),
            'exp' => time() + ((int)$_ENV['AUTH_TOKEN_EXPIRY'] * 2),
        ];

        return [
            'access_token' => JWT::encode($accessPayload, $_ENV['AUTH_SECRET_KEY'], 'HS256'),
            'refresh_token' => JWT::encode($refreshPayload, $_ENV['AUTH_SECRET_KEY'], 'HS256'),
        ];
    }

    public function checkTokenExpired($token): bool
    {
        if (is_null($token)) {
            return false;
        }
        try {
            $decoded = JWT::decode($token, new Key($_ENV['AUTH_SECRET_KEY'], 'HS256'));
            if (!isset($decoded->exp) || $decoded->exp < time()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            LoggerHelper::logError("Token validation error: " . $e->getMessage());
            return false;
        }
    }

    public function isValidToken($token): bool
    {
        return $this->checkTokenExpired($token);
    }

    public function getUserIdFromToken($token): ?int
    {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['AUTH_SECRET_KEY'], 'HS256'));
            return $decoded->sub ?? null;
        } catch (\Exception $e) {
            LoggerHelper::logError("Error decoding token: " . $e->getMessage());
            return null;
        }
    }

    public function invalidateToken($token): void
    {
        /// implementar ainda
    }
}
