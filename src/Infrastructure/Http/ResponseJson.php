<?php

namespace School\Infrastructure\Http;

class ResponseJson
{
    public static function send(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(mixed $data, string $message = 'OK', int $statusCode = 200): void
    {
        self::send([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    public static function created(mixed $data, string $message = 'Created'): void
    {
        self::success($data, $message, 201);
    }

    public static function error(string $message, int $statusCode = 400): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], $statusCode);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }
}
