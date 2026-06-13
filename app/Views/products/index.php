<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h2 class="h4 mb-0">Productos</h2><p class="text-muted mb-0">Consulta disponibilidad y administra el catálogo.</p></div>
    <?php if ($canManage): ?><a class="btn btn-dark" href="<?= e(url('productos/crear')) ?>">Nuevo producto</a><?php endif; ?>
</div>
<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Disponible hoy</th><th>Estado</th><?php if ($canManage): ?><th class="text-end">Acciones</th><?php endif; ?></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><div class="fw-semibold"><?= e($product['name']) ?> <?= (int) $product['is_combo'] === 1 ? '<span class="badge text-bg-warning">Combo</span>' : '' ?></div><small class="text-muted"><?= e($product['description'] ?? '') ?></small></td>
                    <td><?= e($product['category_name']) ?></td>
                    <td><?= e(money((float) $product['price'])) ?></td>
                    <td><?= (int) $product['stock_today'] > 0 ? (int) $product['stock_today'] : '<span class="badge text-bg-danger">AGOTADO</span>' ?></td>
                    <td><span class="badge <?= $product['status'] === 'activo' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= e($product['status']) ?></span></td>
                    <?php if ($canManage): ?>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(url('productos/editar&id=' . $product['id'])) ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= e(url('productos/estado')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $product['id'] ?>"><button class="btn btn-sm btn-outline-warning">Estado</button></form>
                            <form class="d-inline" method="post" action="<?= e(url('productos/eliminar')) ?>" onsubmit="return confirm('¿Eliminar producto?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $product['id'] ?>"><button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
