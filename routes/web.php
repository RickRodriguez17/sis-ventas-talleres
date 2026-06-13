<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Core\Router;

$router = new Router();

$router->get('', [DashboardController::class, 'index']);
$router->get('dashboard', [DashboardController::class, 'index']);
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->post('logout', [AuthController::class, 'logout']);

return $router;
