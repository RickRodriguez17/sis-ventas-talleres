<div class="row g-4">
    <div class="col-lg-5">
        <div class="card table-card mb-4">
            <div class="card-body">
                <h2 class="h5">Caja actual</h2>
                <?php if ($active === null): ?>
                    <form method="post" action="<?= e(url('caja/abrir')) ?>" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-12"><label class="form-label">Monto inicial</label><input class="form-control" type="number" step="0.01" min="0" name="opening_amount" required></div>
                        <div class="col-12"><label class="form-label">Notas</label><textarea class="form-control" name="notes"></textarea></div>
                        <div class="col-12"><button class="btn btn-dark">Abrir caja</button></div>
                    </form>
                <?php else: ?>
                    <p><span class="badge text-bg-success">Abierta</span> <?= e($active['code']) ?></p>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><div class="p-3 bg-light rounded">Inicial<br><strong><?= e(money((float) $summary['opening'])) ?></strong></div></div>
                        <div class="col-6"><div class="p-3 bg-light rounded">Ventas<br><strong><?= e(money((float) $summary['sales'])) ?></strong></div></div>
                        <div class="col-6"><div class="p-3 bg-light rounded">Ingresos<br><strong><?= e(money((float) $summary['income'])) ?></strong></div></div>
                        <div class="col-6"><div class="p-3 bg-light rounded">Esperado<br><strong><?= e(money((float) $summary['expected'])) ?></strong></div></div>
                    </div>
                    <form method="post" action="<?= e(url('caja/cerrar')) ?>" class="row g-2">
                        <?= csrf_field() ?>
                        <div class="col-12"><input class="form-control" type="number" step="0.01" min="0" name="closing_amount" placeholder="Monto contado" required></div>
                        <div class="col-12"><input class="form-control" name="notes" placeholder="Observación de cierre"></div>
                        <div class="col-12"><button class="btn btn-danger" onclick="return confirm('¿Cerrar caja?')">Cerrar caja</button></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($active !== null): ?>
            <div class="card table-card"><div class="card-body"><h2 class="h5">Ingreso / Egreso</h2><form class="row g-2" method="post" action="<?= e(url('caja/movimiento')) ?>"><?= csrf_field() ?><div class="col-md-4"><select class="form-select" name="type"><option value="ingreso">Ingreso</option><option value="egreso">Egreso</option></select></div><div class="col-md-4"><input class="form-control" name="concept" placeholder="Concepto" required></div><div class="col-md-4"><input class="form-control" type="number" step="0.01" min="0.01" name="amount" placeholder="Monto" required></div><div class="col-12"><button class="btn btn-dark">Registrar</button></div></form></div></div>
        <?php endif; ?>
    </div>
    <div class="col-lg-7">
        <div class="card table-card mb-4"><div class="card-body"><h2 class="h5">Movimientos</h2><div class="table-responsive"><table class="table"><thead><tr><th>Tipo</th><th>Concepto</th><th>Monto</th><th>Fecha</th></tr></thead><tbody><?php foreach ($movements as $movement): ?><tr><td><?= e($movement['type']) ?></td><td><?= e($movement['concept']) ?></td><td><?= e(money((float) $movement['amount'])) ?></td><td><?= e($movement['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
        <div class="card table-card"><div class="card-body"><h2 class="h5">Últimas cajas</h2><div class="table-responsive"><table class="table"><thead><tr><th>Código</th><th>Usuario</th><th>Estado</th><th>Apertura</th><th>Diferencia</th></tr></thead><tbody><?php foreach ($history as $row): ?><tr><td><?= e($row['code']) ?></td><td><?= e($row['user_name']) ?></td><td><?= e($row['status']) ?></td><td><?= e($row['opened_at']) ?></td><td><?= e(money((float) ($row['difference_amount'] ?? 0))) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
</div>
