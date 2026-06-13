<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $route = ''): string
{
    $baseUrl = (string) Config::get('app.base_url', '');
    $route = trim($route, '/');

    if ($route === '') {
        return $baseUrl . '/';
    }

    return $baseUrl . '/index.php?route=' . urlencode($route);
}

function asset(string $path): string
{
    $baseUrl = (string) Config::get('app.base_url', '');

    return $baseUrl . '/assets/' . ltrim($path, '/');
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(Csrf::token()) . '">';
}

function money(float $amount, string $currency = 'Bs'): string
{
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}
