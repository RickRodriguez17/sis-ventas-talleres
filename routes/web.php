<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CashController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\ProductionController;
use App\Controllers\ReportController;
use App\Controllers\SaleController;
use App\Controllers\SettingController;
use App\Controllers\UserController;
use App\Core\Router;

$router = new Router();

$router->get('', [DashboardController::class, 'index']);
$router->get('dashboard', [DashboardController::class, 'index']);
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->post('logout', [AuthController::class, 'logout']);
$router->get('categorias', [CategoryController::class, 'index']);
$router->get('categorias/crear', [CategoryController::class, 'create']);
$router->post('categorias/guardar', [CategoryController::class, 'store']);
$router->get('categorias/editar', [CategoryController::class, 'edit']);
$router->post('categorias/actualizar', [CategoryController::class, 'update']);
$router->post('categorias/eliminar', [CategoryController::class, 'delete']);
$router->post('categorias/estado', [CategoryController::class, 'toggle']);
$router->get('productos', [ProductController::class, 'index']);
$router->get('productos/crear', [ProductController::class, 'create']);
$router->post('productos/guardar', [ProductController::class, 'store']);
$router->get('productos/editar', [ProductController::class, 'edit']);
$router->post('productos/actualizar', [ProductController::class, 'update']);
$router->post('productos/eliminar', [ProductController::class, 'delete']);
$router->post('productos/estado', [ProductController::class, 'toggle']);
$router->get('produccion', [ProductionController::class, 'index']);
$router->get('produccion/crear', [ProductionController::class, 'create']);
$router->post('produccion/guardar', [ProductionController::class, 'store']);
$router->get('produccion/historial', [ProductionController::class, 'history']);
$router->get('caja', [CashController::class, 'index']);
$router->post('caja/abrir', [CashController::class, 'open']);
$router->post('caja/movimiento', [CashController::class, 'movement']);
$router->post('caja/cerrar', [CashController::class, 'close']);
$router->get('ventas', [SaleController::class, 'pos']);
$router->post('ventas/guardar', [SaleController::class, 'store']);
$router->get('ventas/ticket', [SaleController::class, 'ticket']);
$router->post('ventas/anular', [SaleController::class, 'void']);
$router->get('pedidos', [OrderController::class, 'index']);
$router->post('pedidos/estado', [OrderController::class, 'updateStatus']);
$router->get('pantalla-pedidos', [OrderController::class, 'readyScreen']);
$router->get('pedidos/listos-json', [OrderController::class, 'readyJson']);
$router->get('usuarios', [UserController::class, 'index']);
$router->get('usuarios/crear', [UserController::class, 'create']);
$router->post('usuarios/guardar', [UserController::class, 'store']);
$router->get('usuarios/editar', [UserController::class, 'edit']);
$router->post('usuarios/actualizar', [UserController::class, 'update']);
$router->post('usuarios/estado', [UserController::class, 'toggle']);
$router->get('configuracion', [SettingController::class, 'edit']);
$router->post('configuracion/actualizar', [SettingController::class, 'update']);
$router->get('reportes', [ReportController::class, 'index']);
$router->get('reportes/exportar', [ReportController::class, 'export']);

return $router;
