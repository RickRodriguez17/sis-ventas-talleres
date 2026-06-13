<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h2 class="h4 mb-0">Pedidos</h2><p class="text-muted mb-0">Gestiona estados: pendiente, preparación, listo y entregado.</p></div>
    <form method="get" class="d-flex gap-2"><input type="hidden" name="route" value="pedidos"><select class="form-select" name="status"><option value="">Todos</option><?php foreach (['pendiente','en_preparacion','listo','entregado'] as $state): ?><option value="<?= e($state) ?>" <?= $status === $state ? 'selected' : '' ?>><?= e($state) ?></option><?php endforeach; ?></select><button class="btn btn-outline-secondary">Filtrar</button></form>
</div>
<div class="card table-card table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Cajero</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td class="fw-bold fs-5"><?= e($order['order_number']) ?></td><td><?= e($order['created_at']) ?></td><td><?= e(money((float) $order['total'])) ?></td><td><span class="badge text-bg-info"><?= e($order['order_status']) ?></span></td><td><?= e($order['user_name']) ?></td>
                <td class="text-end">
                    <form class="d-inline" method="post" action="<?= e(url('pedidos/estado')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $order['id'] ?>"><select class="form-select form-select-sm d-inline w-auto" name="status"><?php foreach (['pendiente','en_preparacion','listo','entregado'] as $state): ?><option value="<?= e($state) ?>" <?= $order['order_status'] === $state ? 'selected' : '' ?>><?= e($state) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-dark">Cambiar</button></form>
                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('ventas/ticket&id=' . $order['id'])) ?>">Ticket</a>
                    <?php if (\App\Core\Auth::isAdmin() && $order['status'] !== 'anulada'): ?><form class="d-inline" method="post" action="<?= e(url('ventas/anular')) ?>" onsubmit="return confirm('¿Anular venta?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $order['id'] ?>"><input type="hidden" name="reason" value="Anulación desde pedidos"><button class="btn btn-sm btn-outline-danger">Anular</button></form><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
