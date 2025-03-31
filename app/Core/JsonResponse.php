<?php

namespace App\Core;

use Exception;

class JsonResponse
{
    public static function success(array $data = [], string $message = "Opération réussie", int $status = 200, array $headers = []): void
    {
        self::send([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status, $headers);
    }

    public static function error(string $message, int $status = 400, array $headers = []): void
    {
        self::send([
            'status' => 'error',
            'message' => $message
        ], $status, $headers);
    }

    private static function send(array $response, int $status, array $headers = []): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json');
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
        }

        $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if ($json === false) {
            throw new Exception("Erreur d'encodage JSON : " . json_last_error_msg());
        }

        echo $json;
        exit;
    }
}
