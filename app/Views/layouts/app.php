<?php

use App\Core\Auth;
use App\Core\Flash;

$user = Auth::user();
$role = Auth::role();
$menu = [
    ['icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'route' => 'dashboard', 'enabled' => true, 'roles' => ['administrador', 'cajero']],
    ['icon' => 'bi-box-seam', 'label' => 'Productos', 'route' => 'productos', 'enabled' => false, 'roles' => ['administrador', 'cajero']],
    ['icon' => 'bi-tags', 'label' => 'Categorías', 'route' => 'categorias', 'enabled' => false, 'roles' => ['administrador']],
    ['icon' => 'bi-basket2', 'label' => 'Producción', 'route' => 'produccion', 'enabled' => false, 'roles' => ['administrador', 'cajero']],
    ['icon' => 'bi-cart-check', 'label' => 'Caja / POS', 'route' => 'ventas', 'enabled' => false, 'roles' => ['administrador', 'cajero']],
    ['icon' => 'bi-receipt', 'label' => 'Pedidos', 'route' => 'pedidos', 'enabled' => false, 'roles' => ['administrador', 'cajero']],
    ['icon' => 'bi-people', 'label' => 'Usuarios', 'route' => 'usuarios', 'enabled' => false, 'roles' => ['administrador']],
    ['icon' => 'bi-bar-chart', 'label' => 'Reportes', 'route' => 'reportes', 'enabled' => false, 'roles' => ['administrador']],
    ['icon' => 'bi-gear', 'label' => 'Configuración', 'route' => 'configuracion', 'enabled' => false, 'roles' => ['administrador']],
];
$success = Flash::get('success');
$error = Flash::get('error');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Sistema de Ventas') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body>
    <aside class="sidebar p-3">
        <div class="sidebar-brand pb-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-shop fs-4"></i></span>
                <div>
                    <strong>FastFood Ventas</strong>
                    <div class="small text-white-50">Autoservicio</div>
                </div>
            </div>
        </div>

        <nav class="nav flex-column">
            <?php foreach ($menu as $item): ?>
                <?php if (!in_array($role, $item['roles'], true)) {
                    continue;
                } ?>
                <?php if ($item['enabled']): ?>
                    <a class="nav-link active" href="<?= e(url($item['route'])) ?>">
                        <i class="bi <?= e($item['icon']) ?> me-2"></i><?= e($item['label']) ?>
                    </a>
                <?php else: ?>
                    <span class="nav-link disabled">
                        <i class="bi <?= e($item['icon']) ?> me-2"></i><?= e($item['label']) ?>
                        <span class="badge badge-soft ms-2">Próx.</span>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="topbar px-3 px-lg-4 py-3">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-sidebar-toggle>
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h1 class="h5 mb-0"><?= e($title ?? 'Dashboard') ?></h1>
                        <span class="small text-muted">Panel de gestión para ventas y producción</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-semibold"><?= e($user['name'] ?? 'Usuario') ?></div>
                        <div class="small text-muted"><?= e($user['role_name'] ?? '') ?></div>
                    </div>
                    <form method="post" action="<?= e(url('logout')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-dark" type="submit">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-3 p-lg-4">
            <?php if ($success !== null): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>
            <?php if ($error !== null): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
