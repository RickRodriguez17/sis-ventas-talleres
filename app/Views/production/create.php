<form method="post" action="<?= e(url('produccion/guardar')) ?>" class="card table-card">
    <div class="card-body">
        <?= csrf_field() ?>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><label class="form-label">Fecha</label><input class="form-control" type="date" name="production_date" value="<?= e(today()) ?>" required></div>
            <div class="col-md-8"><label class="form-label">Notas</label><input class="form-control" name="notes" placeholder="Producción del turno mañana..."></div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Producto</th><th>Cantidad producida</th><th>Alerta bajo stock</th><th>Costo unitario</th></tr></thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <?php if ((int) $product['is_combo'] === 1) { continue; } ?>
                    <tr>
                        <td><?= e($product['name']) ?></td>
                        <td><input class="form-control" type="number" min="0" name="items[<?= (int) $product['id'] ?>][quantity]" value="0"></td>
                        <td><input class="form-control" type="number" min="1" name="items[<?= (int) $product['id'] ?>][low_stock_alert]" value="5"></td>
                        <td><input class="form-control" type="number" min="0" step="0.01" name="items[<?= (int) $product['id'] ?>][unit_cost]" value="0"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-dark">Guardar producción</button>
        <a class="btn btn-outline-secondary" href="<?= e(url('produccion')) ?>">Cancelar</a>
    </div>
</form>
