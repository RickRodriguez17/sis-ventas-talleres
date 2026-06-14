<?php

use App\Core\Auth;

$currency = (string) ($settings['currency'] ?? 'Bs');
?>
<?php if (Auth::isAdmin()): ?>
    <div class="row g-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Ventas del día</span>
                    <h2 class="h5 mb-0"><?= e(money((float) $stats['sales_today'], $currency)) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Ventas del mes</span>
                    <h2 class="h5 mb-0"><?= e(money((float) $stats['sales_month'], $currency)) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Productos vendidos hoy</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['products_sold_today'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Producción restante</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['remaining_production'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Productos agotados</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['exhausted_products'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Total de pedidos hoy</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['orders_total'] ?></h2>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Estado de caja</span>
                    <h2 class="h5 mb-0"><?= $stats['cash_open'] ? 'Abierta' : 'Sin apertura' ?></h2>
                    <span class="small text-muted"><?= e($stats['cash_code'] ?? 'Debes abrir caja') ?></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Ventas del turno</span>
                    <h2 class="h5 mb-0"><?= e(money((float) $stats['sales_shift'], $currency)) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Pedidos del turno</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['orders_shift'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <span class="text-muted small">Productos disponibles</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['products_available'] ?></h2>
                    <span class="small text-muted"><?= (int) $stats['low_stock'] ?> con stock bajo</span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
