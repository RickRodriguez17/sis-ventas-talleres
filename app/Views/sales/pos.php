<?php $productsJson = json_encode($products, JSON_THROW_ON_ERROR); ?>
<?php if ($activeCash === null): ?>
    <div class="alert alert-warning">Debes <a href="<?= e(url('caja')) ?>">abrir caja</a> antes de registrar ventas.</div>
<?php endif; ?>
<form method="post" action="<?= e(url('ventas/guardar')) ?>" id="posForm">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card table-card">
                <div class="card-body">
                    <input class="form-control mb-3" id="productSearch" placeholder="Buscar producto...">
                    <div class="row g-3" id="productGrid">
                        <?php foreach ($products as $product): ?>
                            <?php $available = (int) $product['available_quantity']; ?>
                            <div class="col-sm-6 col-lg-4 product-item" data-name="<?= e(strtolower($product['name'])) ?>">
                                <button type="button" class="card product-card w-100 h-100 text-start p-2 <?= $available <= 0 ? 'opacity-50' : '' ?>" data-add-product="<?= (int) $product['id'] ?>" <?= $available <= 0 ? 'disabled' : '' ?>>
                                    <?php if (!empty($product['image'])): ?><img class="product-thumb mb-2" src="<?= e(public_file($product['image'])) ?>" alt="<?= e($product['name']) ?>"><?php else: ?><div class="product-thumb mb-2"><i class="bi bi-image text-muted fs-1"></i></div><?php endif; ?>
                                    <strong><?= e($product['name']) ?></strong>
                                    <span class="text-muted small d-block"><?= e(money((float) $product['price'])) ?></span>
                                    <span class="badge <?= $available > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $available > 0 ? 'Disp. ' . $available : 'AGOTADO' ?></span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card table-card sticky-top" style="top:90px">
                <div class="card-body">
                    <h2 class="h5">Carrito</h2>
                    <div class="table-responsive">
                        <table class="table align-middle" id="cartTable"><thead><tr><th>Producto</th><th>Cant.</th><th>Total</th><th></th></tr></thead><tbody></tbody></table>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><label class="form-label">Descuento</label><select class="form-select" name="discount_type" id="discountType"><option value="none">Sin descuento</option><option value="percentage">Porcentaje</option><option value="fixed">Monto fijo</option></select></div>
                        <div class="col-6"><label class="form-label">Valor</label><input class="form-control" type="number" step="0.01" min="0" name="discount_value" id="discountValue" value="0"></div>
                        <div class="col-6"><label class="form-label">Pago</label><select class="form-select" name="payment_method"><option value="efectivo">Efectivo</option><option value="qr">QR</option><option value="tarjeta">Tarjeta</option></select></div>
                        <div class="col-6"><label class="form-label">Recibido</label><input class="form-control" type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" value="0"></div>
                        <div class="col-12"><label class="form-label">Observaciones</label><textarea class="form-control" name="observations" rows="2"></textarea></div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="subtotalText">Bs 0.00</strong></div>
                    <div class="d-flex justify-content-between"><span>Descuento</span><strong id="discountText">Bs 0.00</strong></div>
                    <div class="d-flex justify-content-between fs-4"><span>Total</span><strong id="totalText">Bs 0.00</strong></div>
                    <div class="d-flex justify-content-between"><span>Cambio</span><strong id="changeText">Bs 0.00</strong></div>
                    <button class="btn btn-dark w-100 mt-3" <?= $activeCash === null ? 'disabled' : '' ?>>Cobrar e imprimir ticket</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
const products = <?= $productsJson ?>;
const cart = new Map();
const money = value => 'Bs ' + Number(value).toFixed(2);
function renderCart() {
    const tbody = document.querySelector('#cartTable tbody');
    tbody.innerHTML = '';
    let subtotal = 0;
    for (const [id, quantity] of cart.entries()) {
        const product = products.find(item => Number(item.id) === Number(id));
        const total = Number(product.price) * quantity;
        subtotal += total;
        tbody.insertAdjacentHTML('beforeend', `<tr><td>${product.name}<input type="hidden" name="items[${id}]" value="${quantity}"></td><td><input class="form-control form-control-sm cart-qty" data-id="${id}" type="number" min="1" max="${product.available_quantity}" value="${quantity}"></td><td>${money(total)}</td><td><button class="btn btn-sm btn-outline-danger" type="button" data-remove="${id}">×</button></td></tr>`);
    }
    const type = document.getElementById('discountType').value;
    const value = Number(document.getElementById('discountValue').value || 0);
    const discount = type === 'percentage' ? subtotal * Math.min(value, 100) / 100 : (type === 'fixed' ? Math.min(value, subtotal) : 0);
    const total = Math.max(0, subtotal - discount);
    const paid = Number(document.getElementById('paidAmount').value || 0);
    document.getElementById('subtotalText').textContent = money(subtotal);
    document.getElementById('discountText').textContent = money(discount);
    document.getElementById('totalText').textContent = money(total);
    document.getElementById('changeText').textContent = money(Math.max(0, paid - total));
}
document.addEventListener('click', event => {
    const addButton = event.target.closest('[data-add-product]');
    if (addButton) {
        const id = Number(addButton.dataset.addProduct);
        const product = products.find(item => Number(item.id) === id);
        const current = cart.get(id) || 0;
        if (current < Number(product.available_quantity)) cart.set(id, current + 1);
        renderCart();
    }
    const removeButton = event.target.closest('[data-remove]');
    if (removeButton) { cart.delete(Number(removeButton.dataset.remove)); renderCart(); }
});
document.addEventListener('input', event => {
    if (event.target.classList.contains('cart-qty')) cart.set(Number(event.target.dataset.id), Number(event.target.value || 1));
    if (event.target.id === 'productSearch') {
        const term = event.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => item.classList.toggle('d-none', !item.dataset.name.includes(term)));
    }
    renderCart();
});
document.getElementById('posForm').addEventListener('submit', event => {
    if (cart.size === 0) { event.preventDefault(); alert('Agrega productos al carrito.'); }
});
</script>
