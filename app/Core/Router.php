<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        $route = $this->normalize($path);
        $handler = $this->routes[$method][$route] ?? null;

        if ($handler === null) {
            http_response_code(404);
            (new \App\Controllers\ErrorController())->notFound();
            return;
        }

        [$controller, $action] = $handler;
        (new $controller())->$action();
    }

    private function normalize(string $path): string
    {
        $path = trim($path, '/');

        return $path === '' ? 'dashboard' : $path;
    }
}
