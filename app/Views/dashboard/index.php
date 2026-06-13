<?php

use App\Core\Auth;

$currency = (string) ($settings['currency'] ?? 'Bs');
$labels = array_map(static fn (string $date): string => date('d/m', strtotime($date)), array_keys($dailySales));
$values = array_values($dailySales);
?>
<?php if (Auth::isAdmin()): ?>
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Ventas del día</span>
                        <h2 class="h4 mb-0"><?= e(money((float) $stats['sales_today'], $currency)) ?></h2>
                    </div>
                    <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-cash-stack fs-4"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Ventas del mes</span>
                        <h2 class="h4 mb-0"><?= e(money((float) $stats['sales_month'], $currency)) ?></h2>
                    </div>
                    <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-calendar3 fs-4"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Productos vendidos hoy</span>
                        <h2 class="h4 mb-0"><?= (int) $stats['products_sold_today'] ?></h2>
                    </div>
                    <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-bag-check fs-4"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Producción restante</span>
                        <h2 class="h4 mb-0"><?= (int) $stats['remaining_production'] ?></h2>
                    </div>
                    <span class="stat-icon bg-info-subtle text-info"><i class="bi bi-basket fs-4"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Productos agotados</span>
                        <h2 class="h4 mb-0"><?= (int) $stats['exhausted_products'] ?></h2>
                    </div>
                    <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-octagon fs-4"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between gap-3">
                    <div>
                        <span class="text-muted small">Total de pedidos hoy</span>
                        <h2 class="h4 mb-0"><?= (int) $stats['orders_total'] ?></h2>
                    </div>
                    <span class="stat-icon bg-secondary-subtle text-secondary"><i class="bi bi-receipt-cutoff fs-4"></i></span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="text-muted small">Estado de caja</span>
                    <h2 class="h5 mb-0"><?= $stats['cash_open'] ? 'Abierta' : 'Sin apertura' ?></h2>
                    <span class="small text-muted"><?= e($stats['cash_code'] ?? 'Debes abrir caja') ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="text-muted small">Ventas del turno</span>
                    <h2 class="h5 mb-0"><?= e(money((float) $stats['sales_shift'], $currency)) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="text-muted small">Pedidos del turno</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['orders_shift'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="text-muted small">Productos disponibles</span>
                    <h2 class="h5 mb-0"><?= (int) $stats['products_available'] ?></h2>
                    <span class="small text-warning"><?= (int) $stats['low_stock'] ?> con stock bajo</span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card chart-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-0">Ventas diarias</h2>
                        <span class="text-muted small">Últimos 7 días</span>
                    </div>
                    <span class="badge badge-soft">Dashboard inicial</span>
                </div>
                <canvas id="dailySalesChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card table-card h-100">
            <div class="card-body">
                <h2 class="h5">Alcance del módulo</h2>
                <p class="text-muted">Esta primera entrega deja lista la base técnica para desarrollar productos, producción, POS, pedidos, caja, reportes y configuración por módulos.</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0"><i class="bi bi-check2-circle text-success me-2"></i>MVC PHP puro</li>
                    <li class="list-group-item px-0"><i class="bi bi-check2-circle text-success me-2"></i>PDO con consultas preparadas</li>
                    <li class="list-group-item px-0"><i class="bi bi-check2-circle text-success me-2"></i>Roles administrador/cajero</li>
                    <li class="list-group-item px-0"><i class="bi bi-check2-circle text-success me-2"></i>Base `db_ventas` normalizada</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('dailySalesChart');

    if (!canvas) {
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels, JSON_THROW_ON_ERROR) ?>,
            datasets: [{
                label: 'Ventas',
                data: <?= json_encode($values, JSON_THROW_ON_ERROR) ?>,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, .16)',
                fill: true,
                tension: .35
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
