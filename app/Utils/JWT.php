<?php

namespace App\Utils;

class JWT
{
    //stocker dans une variable d'environnement
    private static string $secretKey;
    private static string $algo = "HS256";
    private static int $expiry = 3600;

    public static   function init(): void
    {
        self::$secretKey =  $_ENV['SECRET_KEY'] ?? 'default-secret-key';
    }

    public static function getSecretKey(): string
    {
        return self::$secretKey;
    }

    public static function generateToken(array $payload): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algo]);
        $payload['exp'] = time() + self::$expiry; // Ajoute l'expiration
        $payload = json_encode($payload);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", self::$secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    public static function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $parts;
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        if (!$payload || !isset($payload['exp']) || time() > $payload['exp']) {
            return null; // Token expiré
        }

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", self::$secretKey, true);
        $validSignature = self::base64UrlEncode($signature);

        return hash_equals($validSignature, $base64UrlSignature) ? $payload : null;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
