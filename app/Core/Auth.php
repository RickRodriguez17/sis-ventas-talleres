<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = (new User())->findActiveByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        (new User())->updateLastLogin((int) $user['id']);

        return true;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user === null ? null : (int) $user['id'];
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        $user = self::user();

        return $user['role_slug'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'administrador';
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . url('login'));
            exit;
        }
    }

    public static function requireGuest(): void
    {
        if (self::check()) {
            header('Location: ' . url('dashboard'));
            exit;
        }
    }
}
