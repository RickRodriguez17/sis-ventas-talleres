<div class="d-flex justify-content-between mb-3"><div><h2 class="h4 mb-0">Usuarios</h2><p class="text-muted mb-0">Administra accesos de administradores y cajeros.</p></div><a class="btn btn-dark" href="<?= e(url('usuarios/crear')) ?>">Nuevo usuario</a></div>
<div class="card table-card table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Teléfono</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
        <tbody><?php foreach ($users as $userRow): ?><tr><td><?= e($userRow['name']) ?></td><td><?= e($userRow['email']) ?></td><td><?= e($userRow['role_name']) ?></td><td><?= e($userRow['phone'] ?? '') ?></td><td><span class="badge <?= (int) $userRow['status'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (int) $userRow['status'] === 1 ? 'Activo' : 'Inactivo' ?></span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= e(url('usuarios/editar&id=' . $userRow['id'])) ?>">Editar</a><form class="d-inline" method="post" action="<?= e(url('usuarios/estado')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $userRow['id'] ?>"><button class="btn btn-sm btn-outline-warning">Estado</button></form></td></tr><?php endforeach; ?></tbody>
    </table>
</div>
