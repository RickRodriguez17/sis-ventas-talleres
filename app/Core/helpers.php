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
    $baseUrl = app_base_url();
    $route = trim($route, '/');

    if ($route === '') {
        return $baseUrl . '/';
    }

    return $baseUrl . '/index.php?route=' . urlencode($route);
}

function asset(string $path): string
{
    $assetBase = app_asset_base_url();

    return $assetBase . '/' . ltrim($path, '/');
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
