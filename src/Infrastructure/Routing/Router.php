<?php

namespace School\Infrastructure\Routing;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void //registra una ruta GET
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void //registra una ruta POST
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Eliminar query string
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                call_user_func($route['handler']);
                return;
            }
        }

        // Ruta no encontrada
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
