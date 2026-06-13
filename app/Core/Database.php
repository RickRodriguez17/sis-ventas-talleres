<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = Config::get('database.host');
        $database = Config::get('database.database');
        $charset = Config::get('database.charset', 'utf8mb4');
        $username = Config::get('database.username');
        $password = Config::get('database.password');
        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";

        try {
            self::$connection = new PDO($dsn, (string) $username, (string) $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new PDOException('No se pudo conectar con la base de datos.', (int) $exception->getCode(), $exception);
        }

        return self::$connection;
    }
}
