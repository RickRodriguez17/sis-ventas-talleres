<?php

declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function get(string $type): ?string
    {
        if (!isset($_SESSION['_flash'][$type])) {
            return null;
        }

        $message = (string) $_SESSION['_flash'][$type];
        unset($_SESSION['_flash'][$type]);

        return $message;
    }
}
