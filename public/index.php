<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/app/Core/Autoloader.php';

\App\Core\Autoloader::register();

session_name((string) \App\Core\Config::get('app.session_name', 'sis_ventas_session'));
session_start();

require ROOT_PATH . '/app/Core/helpers.php';

$route = (string) ($_GET['route'] ?? '');
$method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
$router = require ROOT_PATH . '/routes/web.php';
$router->dispatch($method, $route);
