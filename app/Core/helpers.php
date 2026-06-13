<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $route = ''): string
{
    $baseUrl = app_base_url();
    $route = trim($route, '/');

    if ($route === '') {
        return $baseUrl . '/';
    }

    return $baseUrl . '/index.php?route=' . $route;
}

function asset(string $path): string
{
    $assetBase = app_asset_base_url();

    return $assetBase . '/' . ltrim($path, '/');
}

function public_file(string $path): string
{
    $base = app_base_url();

    if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === 'index.php'
        && basename(dirname((string) ($_SERVER['SCRIPT_FILENAME'] ?? ''))) === 'public') {
        return $base . '/' . ltrim($path, '/');
    }

    return $base . '/public/' . ltrim($path, '/');
}

function app_base_url(): string
{
    $configuredUrl = (string) Config::get('app.base_url', '');

    if ($configuredUrl !== '') {
        return rtrim($configuredUrl, '/');
    }

    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    return $basePath === '.' ? '' : $basePath;
}

function app_asset_base_url(): string
{
    $configuredAssetUrl = (string) Config::get('app.asset_url', '');

    if ($configuredAssetUrl !== '') {
        return rtrim($configuredAssetUrl, '/');
    }

    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptFile = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($basePath === '.') {
        $basePath = '';
    }

    if (basename(dirname($scriptFile)) === 'public') {
        return $basePath . '/assets';
    }

    return $basePath . '/public/assets';
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(Csrf::token()) . '">';
}

function money(float $amount, string $currency = 'Bs'): string
{
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}

function slugify(string $value): string
{
    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    $value = strtolower((string) $value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value === '' ? 'item-' . time() : $value;
}

function redirect_back(string $fallback = 'dashboard'): void
{
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');

    header('Location: ' . ($referer !== '' ? $referer : url($fallback)));
    exit;
}

function today(): string
{
    return date('Y-m-d');
}
