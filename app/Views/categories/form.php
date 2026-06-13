<?php $isEdit = $category !== null; ?>
<div class="card table-card">
    <div class="card-body">
        <form method="post" action="<?= e(url($isEdit ? 'categorias/actualizar&id=' . $category['id'] : 'categorias/guardar')) ?>" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input class="form-control" name="name" required value="<?= e($category['name'] ?? '') ?>">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="status" id="status" <?= !$isEdit || (int) $category['status'] === 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status">Activa</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="description" rows="3"><?= e($category['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-dark">Guardar</button>
                <a class="btn btn-outline-secondary" href="<?= e(url('categorias')) ?>">Cancelar</a>
            </div>
        </form>
    </div>
</div>
