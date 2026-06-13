<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'app'): void
    {
        $viewPath = ROOT_PATH . '/app/Views/' . $view . '.php';
        $layoutPath = ROOT_PATH . '/app/Views/layouts/' . $layout . '.php';

        if (!is_file($viewPath) || !is_file($layoutPath)) {
            http_response_code(500);
            echo 'Vista no encontrada.';
            return;
        }

        foreach ($data as $key => $value) {
            $$key = $value;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    protected function redirect(string $route): void
    {
        header('Location: ' . url($route));
        exit;
    }
}
