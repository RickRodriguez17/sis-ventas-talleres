<form method="post" enctype="multipart/form-data" action="<?= e(url('configuracion/actualizar')) ?>" class="card table-card">
    <div class="card-body row g-3">
        <?= csrf_field() ?>
        <div class="col-md-6"><label class="form-label">Nombre del negocio</label><input class="form-control" name="business_name" required value="<?= e($settings['business_name'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Moneda</label><input class="form-control" name="currency" value="<?= e($settings['currency'] ?? 'Bs') ?>"></div>
        <div class="col-md-3"><label class="form-label">Logo</label><input class="form-control" type="file" name="logo" accept="image/*"></div>
        <div class="col-md-8"><label class="form-label">Dirección</label><input class="form-control" name="address" value="<?= e($settings['address'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Teléfono</label><input class="form-control" name="phone" value="<?= e($settings['phone'] ?? '') ?>"></div>
        <div class="col-12"><label class="form-label">Datos del ticket / pie</label><textarea class="form-control" name="ticket_footer" rows="4"><?= e($settings['ticket_footer'] ?? '') ?></textarea></div>
        <div class="col-12"><button class="btn btn-dark">Guardar configuración</button></div>
    </div>
</form>
