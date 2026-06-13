<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-0">Categorías</h2>
        <p class="text-muted mb-0">Administra familias como hamburguesas, bebidas y combos.</p>
    </div>
    <a class="btn btn-dark" href="<?= e(url('categorias/crear')) ?>"><i class="bi bi-plus-lg me-1"></i>Nueva</a>
</div>
<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nombre</th><th>Descripción</th><th>Productos</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td class="fw-semibold"><?= e($category['name']) ?></td>
                    <td><?= e($category['description'] ?? '') ?></td>
                    <td><?= (int) $category['products_count'] ?></td>
                    <td><span class="badge <?= (int) $category['status'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (int) $category['status'] === 1 ? 'Activa' : 'Inactiva' ?></span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(url('categorias/editar&id=' . $category['id'])) ?>">Editar</a>
                        <form class="d-inline" method="post" action="<?= e(url('categorias/estado')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $category['id'] ?>"><button class="btn btn-sm btn-outline-warning">Estado</button></form>
                        <form class="d-inline" method="post" action="<?= e(url('categorias/eliminar')) ?>" onsubmit="return confirm('¿Eliminar categoría?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $category['id'] ?>"><button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
