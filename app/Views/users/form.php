<?php $isEdit = $userItem !== null; ?>
<form method="post" action="<?= e(url($isEdit ? 'usuarios/actualizar&id=' . $userItem['id'] : 'usuarios/guardar')) ?>" class="card table-card">
    <div class="card-body row g-3">
        <?= csrf_field() ?>
        <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" name="name" required value="<?= e($userItem['name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Correo</label><input class="form-control" type="email" name="email" required value="<?= e($userItem['email'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Rol</label><select class="form-select" name="role_id"><?php foreach ($roles as $role): ?><option value="<?= (int) $role['id'] ?>" <?= (int) ($userItem['role_id'] ?? 2) === (int) $role['id'] ? 'selected' : '' ?>><?= e($role['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label">Teléfono</label><input class="form-control" name="phone" value="<?= e($userItem['phone'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Contraseña <?= $isEdit ? '(opcional)' : '' ?></label><input class="form-control" type="password" name="password" <?= $isEdit ? '' : 'required' ?>></div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" id="status" <?= !$isEdit || (int) $userItem['status'] === 1 ? 'checked' : '' ?>><label class="form-check-label" for="status">Activo</label></div></div>
        <div class="col-12"><button class="btn btn-dark">Guardar</button><a class="btn btn-outline-secondary" href="<?= e(url('usuarios')) ?>">Cancelar</a></div>
    </div>
</form>
