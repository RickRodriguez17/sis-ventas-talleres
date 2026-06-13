<form class="card table-card mb-4" method="get">
    <div class="card-body row g-3 align-items-end">
        <input type="hidden" name="route" value="reportes">
        <div class="col-md-3"><label class="form-label">Desde</label><input class="form-control" type="date" name="from" value="<?= e($from) ?>"></div>
        <div class="col-md-3"><label class="form-label">Hasta</label><input class="form-control" type="date" name="to" value="<?= e($to) ?>"></div>
        <div class="col-md-6 d-flex gap-2"><button class="btn btn-dark">Filtrar</button><a class="btn btn-outline-success" href="<?= e(url('reportes/exportar&format=xls&from=' . $from . '&to=' . $to)) ?>">Excel</a><a class="btn btn-outline-secondary" href="<?= e(url('reportes/exportar&format=csv&from=' . $from . '&to=' . $to)) ?>">CSV</a><a class="btn btn-outline-danger" target="_blank" href="<?= e(url('reportes/exportar&format=pdf&from=' . $from . '&to=' . $to)) ?>">PDF imprimible</a></div>
    </div>
</form>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card"><div class="card-body"><span class="text-muted">Ventas</span><h2><?= e(money((float) $summary['sales_total'], $settings['currency'] ?? 'Bs')) ?></h2></div></div></div>
    <div class="col-md-4"><div class="card stat-card"><div class="card-body"><span class="text-muted">Pedidos</span><h2><?= (int) $summary['sales_count'] ?></h2></div></div></div>
    <div class="col-md-4"><div class="card stat-card"><div class="card-body"><span class="text-muted">Ganancia estimada</span><h2><?= e(money((float) $summary['sales_total'], $settings['currency'] ?? 'Bs')) ?></h2></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card table-card"><div class="card-body"><h2 class="h5">Productos vendidos</h2><table class="table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Total</th></tr></thead><tbody><?php foreach ($summary['products'] as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= (int) $row['quantity'] ?></td><td><?= e(money((float) $row['total'], $settings['currency'] ?? 'Bs')) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card table-card"><div class="card-body"><h2 class="h5">Métodos de pago</h2><table class="table"><thead><tr><th>Método</th><th>Cantidad</th><th>Total</th></tr></thead><tbody><?php foreach ($summary['payments'] as $row): ?><tr><td><?= e($row['payment_method']) ?></td><td><?= (int) $row['count'] ?></td><td><?= e(money((float) $row['total'], $settings['currency'] ?? 'Bs')) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card table-card"><div class="card-body"><h2 class="h5">Producción</h2><table class="table"><thead><tr><th>Producto</th><th>Producido</th><th>Restante</th></tr></thead><tbody><?php foreach ($production as $row): ?><tr><td><?= e($row['name']) ?></td><td><?= (int) $row['produced'] ?></td><td><?= (int) $row['remaining'] ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card table-card"><div class="card-body"><h2 class="h5">Caja</h2><table class="table"><thead><tr><th>Código</th><th>Usuario</th><th>Estado</th><th>Diferencia</th></tr></thead><tbody><?php foreach ($cash as $row): ?><tr><td><?= e($row['code']) ?></td><td><?= e($row['user_name']) ?></td><td><?= e($row['status']) ?></td><td><?= e(money((float) ($row['difference_amount'] ?? 0), $settings['currency'] ?? 'Bs')) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
