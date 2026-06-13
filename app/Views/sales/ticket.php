<div class="card table-card mx-auto" style="max-width:420px">
    <div class="card-body">
        <div class="text-center">
            <h2 class="h4"><?= e($settings['business_name'] ?? 'FastFood Ventas') ?></h2>
            <p class="mb-1"><?= e($settings['address'] ?? '') ?></p>
            <p class="mb-3"><?= e($settings['phone'] ?? '') ?></p>
            <h3 class="display-6 fw-bold"><?= e($sale['order_number']) ?></h3>
        </div>
        <hr>
        <p class="small mb-1">Fecha: <?= e($sale['created_at']) ?></p>
        <p class="small">Cajero: <?= e($sale['user_name']) ?></p>
        <table class="table table-sm">
            <tbody>
            <?php foreach ($sale['details'] as $detail): ?>
                <tr><td><?= (int) $detail['quantity'] ?> x <?= e($detail['product_name']) ?></td><td class="text-end"><?= e(money((float) $detail['total'], $settings['currency'] ?? 'Bs')) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between"><span>Subtotal</span><strong><?= e(money((float) $sale['subtotal'], $settings['currency'] ?? 'Bs')) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Descuento</span><strong><?= e(money((float) $sale['discount_total'], $settings['currency'] ?? 'Bs')) ?></strong></div>
        <div class="d-flex justify-content-between fs-5"><span>Total</span><strong><?= e(money((float) $sale['total'], $settings['currency'] ?? 'Bs')) ?></strong></div>
        <div class="d-flex justify-content-between"><span>Cambio</span><strong><?= e(money((float) $sale['change_amount'], $settings['currency'] ?? 'Bs')) ?></strong></div>
        <p class="text-center small mt-3"><?= e($settings['ticket_footer'] ?? 'Gracias por su compra') ?></p>
        <div class="d-flex gap-2 no-print"><button class="btn btn-dark w-100" onclick="window.print()">Imprimir</button><a class="btn btn-outline-secondary w-100" href="<?= e(url('ventas')) ?>">Nueva venta</a></div>
    </div>
</div>
