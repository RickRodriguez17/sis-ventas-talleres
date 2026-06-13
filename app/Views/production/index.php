<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h2 class="h4 mb-0">Producción de hoy</h2><p class="text-muted mb-0">Controla unidades disponibles, agotadas y alertas de stock bajo.</p></div>
    <div class="d-flex gap-2"><?php if (\App\Core\Auth::isAdmin()): ?><a class="btn btn-dark" href="<?= e(url('produccion/crear')) ?>">Registrar producción</a><a class="btn btn-outline-secondary" href="<?= e(url('produccion/historial')) ?>">Historial</a><?php endif; ?></div>
</div>
<div class="row g-3">
    <?php foreach ($items as $item): ?>
        <?php $remaining = (int) $item['remaining_quantity']; $low = $remaining > 0 && $remaining <= (int) $item['low_stock_alert']; ?>
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><h3 class="h6 mb-1"><?= e($item['name']) ?></h3><span class="text-muted small"><?= e($item['category_name']) ?></span></div>
                        <?php if ($remaining <= 0): ?><span class="badge text-bg-danger">AGOTADO</span><?php elseif ($low): ?><span class="badge text-bg-warning">Stock bajo</span><?php else: ?><span class="badge text-bg-success">Disponible</span><?php endif; ?>
                    </div>
                    <div class="mt-3 display-6 fw-bold"><?= $remaining ?></div>
                    <div class="text-muted small">Producido: <?= (int) $item['produced_quantity'] ?> | Alerta: <?= (int) $item['low_stock_alert'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
