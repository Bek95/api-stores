<?php

namespace App\Middleware;

use App\Core\JsonResponse;
use App\Utils\JWT;
use Exception;

class AuthMiddleware
{
    public static function handle(): void
    {
        try {
            $headers = getallheaders();

            if (!isset($headers['Authorization'])) {
                JsonResponse::error('Token manquant', 401);
            }

            $tokenParts = explode(' ', $headers['Authorization']);

            if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
                JsonResponse::error('Format du token invalide', 401);
            }

            $token = $tokenParts[1];
            $decoded = JWT::validateToken($token);

            if (!$decoded) {
                JsonResponse::error('Token invalide ou expiré', 401);
            }

            $_SESSION['user'] = $decoded;
        } catch (Exception $e) {
            JsonResponse::error("Une erreur est survenue: " . $e->getMessage(), 500);
        }
    }
}
