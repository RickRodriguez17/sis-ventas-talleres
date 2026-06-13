<div class="d-flex justify-content-between mb-3"><h2 class="h4">Historial de producción</h2><a class="btn btn-outline-secondary" href="<?= e(url('produccion')) ?>">Volver</a></div>
<div class="card table-card table-responsive">
    <table class="table mb-0 align-middle">
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Productos</th><th>Producido</th><th>Restante</th><th>Notas</th></tr></thead>
        <tbody><?php foreach ($batches as $batch): ?><tr><td><?= e($batch['production_date']) ?></td><td><?= e($batch['user_name']) ?></td><td><?= (int) $batch['items_count'] ?></td><td><?= (int) $batch['total_produced'] ?></td><td><?= (int) $batch['total_remaining'] ?></td><td><?= e($batch['notes'] ?? '') ?></td></tr><?php endforeach; ?></tbody>
    </table>
</div>
