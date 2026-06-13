<div class="d-flex justify-content-between no-print mb-3"><button class="btn btn-dark" onclick="window.print()">Imprimir / Guardar PDF</button></div>
<h1 class="h3"><?= e($settings['business_name'] ?? 'FastFood Ventas') ?></h1>
<p>Reporte de ventas del <?= e($from) ?> al <?= e($to) ?></p>
<table class="table table-bordered">
    <thead><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th>Método</th><th>Estado</th></tr></thead>
    <tbody><?php foreach ($sales as $sale): ?><tr><td><?= e($sale['order_number']) ?></td><td><?= e($sale['created_at']) ?></td><td><?= e(money((float) $sale['total'], $settings['currency'] ?? 'Bs')) ?></td><td><?= e($sale['payment_method']) ?></td><td><?= e($sale['status']) ?></td></tr><?php endforeach; ?></tbody>
</table>
