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

    protected function saveUpload(string $field, string $directory, ?string $currentPath = null): ?string
    {
        if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        if (($_FILES[$field]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return $currentPath;
        }

        $tmpName = (string) $_FILES[$field]['tmp_name'];
        $originalName = (string) $_FILES[$field]['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $allowed, true)) {
            return $currentPath;
        }

        $targetDirectory = ROOT_PATH . '/public/uploads/' . trim($directory, '/');

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }

        $fileName = bin2hex(random_bytes(12)) . '.' . $extension;
        $target = $targetDirectory . '/' . $fileName;

        if (!move_uploaded_file($tmpName, $target)) {
            return $currentPath;
        }

        return 'uploads/' . trim($directory, '/') . '/' . $fileName;
    }
}
