<?php
$isEdit = $product !== null;
$comboMap = [];
foreach ($comboItems as $item) {
    $comboMap[(int) $item['product_id']] = (int) $item['quantity'];
}
?>
<form method="post" enctype="multipart/form-data" action="<?= e(url($isEdit ? 'productos/actualizar&id=' . $product['id'] : 'productos/guardar')) ?>" class="card table-card">
    <div class="card-body row g-3">
        <?= csrf_field() ?>
        <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="name" required value="<?= e($product['name'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Categoría</label><select class="form-select" name="category_id" required><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label">Precio</label><input class="form-control" name="price" type="number" min="0.01" step="0.01" required value="<?= e((string) ($product['price'] ?? '')) ?>"></div>
        <div class="col-md-6"><label class="form-label">Imagen</label><input class="form-control" type="file" name="image" accept="image/*"><?php if (!empty($product['image'])): ?><small class="text-muted">Actual: <?= e($product['image']) ?></small><?php endif; ?></div>
        <div class="col-md-3"><label class="form-label">Estado</label><select class="form-select" name="status"><option value="activo" <?= ($product['status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($product['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
        <div class="col-md-3 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_combo" id="is_combo" <?= (int) ($product['is_combo'] ?? 0) === 1 ? 'checked' : '' ?>><label for="is_combo" class="form-check-label">Combo promocional</label></div></div>
        <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" name="description" rows="3"><?= e($product['description'] ?? '') ?></textarea></div>
        <div class="col-12">
            <h3 class="h6">Productos incluidos si es combo</h3>
            <div class="row g-2">
                <?php foreach ($products as $item): ?>
                    <?php if ($isEdit && (int) $item['id'] === (int) $product['id']) { continue; } ?>
                    <div class="col-md-4">
                        <label class="form-label small"><?= e($item['name']) ?></label>
                        <input class="form-control" type="number" min="0" name="combo_items[<?= (int) $item['id'] ?>]" value="<?= (int) ($comboMap[(int) $item['id']] ?? 0) ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-dark">Guardar</button><a class="btn btn-outline-secondary" href="<?= e(url('productos')) ?>">Cancelar</a></div>
    </div>
</form>
