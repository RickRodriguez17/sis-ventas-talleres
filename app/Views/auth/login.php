<?php

use App\Core\Flash;

$error = Flash::get('error');
$success = Flash::get('success');
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card auth-card">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <span class="stat-icon bg-warning-subtle text-warning mb-3"><i class="bi bi-shop fs-3"></i></span>
                    <h1 class="h3 mb-1">FastFood Ventas</h1>
                    <p class="text-muted mb-0">Ingresa para gestionar ventas y producción</p>
                </div>

                <?php if ($success !== null): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if ($error !== null): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= e(url('login')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input class="form-control" id="email" name="email" type="email" autocomplete="email" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                        </div>
                    </div>
                    <button class="btn btn-dark w-100 py-2" type="submit">Iniciar sesión</button>
                </form>

                <div class="bg-light rounded-3 p-3 mt-4 small">
                    <strong>Demo:</strong>
                    <div>Admin: admin@demo.com / admin123</div>
                    <div>Cajero: cajero@demo.com / cajero123</div>
                </div>
            </div>
        </div>
    </div>
</div>
