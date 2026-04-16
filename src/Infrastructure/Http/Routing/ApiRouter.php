<?php

namespace School\Infrastructure\Http\Routing;

use School\Infrastructure\Http\Request;

class ApiRouter
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri    = $request->getUri();

        // Handle CORS preflight
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);
            exit;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== null) {
                $request->setParams($params);
                call_user_func($route['handler'], $request);
                return;
            }
        }

        // Route not found
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found: ' . $method . ' ' . $uri,
            'data'    => null,
        ]);
    }

    /**
     * Match a route pattern against a URI.
     * Returns an array of path parameters if matched, or null if not matched.
     * Pattern: /api/students/{id}  ->  matches /api/students/3 with ['id' => '3']
     */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        // Escape pattern for regex, then replace {param} with named capture groups
        $regexPattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regexPattern = '#^' . $regexPattern . '$#';

        if (preg_match($regexPattern, $uri, $matches)) {
            // Extract only named captures
            $params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
            return $params;
        }

        return null;
    }
}
