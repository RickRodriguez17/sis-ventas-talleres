<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $items = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if ($file === null || $file === '') {
            return $default;
        }

        if (!array_key_exists($file, self::$items)) {
            $path = ROOT_PATH . '/config/' . $file . '.php';
            self::$items[$file] = is_file($path) ? require $path : [];
        }

        $value = self::$items[$file];

        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }

            $value = $value[$part];
        }

        return $value;
    }
}
