<?php

namespace App\Core;

use App\Core\Container;

class Router
{
    private static array $routes = [];

    public static function addRoute(string $method, string $path, array $handler, array $middlewares = []): void
    {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => '#^' . trim($path, '/') . '$#',
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public static function dispatch(): void
    {
        $requestedMethod = $_SERVER['REQUEST_METHOD'];
        $requestedPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach (self::$routes as $route) {
            if ($route['method'] === $requestedMethod && preg_match($route['path'], $requestedPath, $matches)) {
                self::handleRoute($route, $matches);
                return;
            }
        }

        JsonResponse::error('Route not found', 404);
    }

    private static function handleRoute(array $route, array $matches): void
    {
        [$controller, $method] = $route['handler'];

        // Appliquer les middlewares
        foreach ($route['middlewares'] as $middleware) {
            $middlewareInstance = new $middleware();
            $middlewareResponse = $middlewareInstance->handle($matches);

            if ($middlewareResponse instanceof JsonResponse) {
                exit;
            }
        }

        $container = new Container();
        $controllerInstance = $container->make($controller);

        if (!method_exists($controllerInstance, $method)) {
            JsonResponse::error('Method not found', 404);
        }

        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        $response = call_user_func_array([$controllerInstance, $method], $params);

        if ($response instanceof JsonResponse) {
            exit;
        }

        JsonResponse::error('Invalid response type', 500);
    }
}