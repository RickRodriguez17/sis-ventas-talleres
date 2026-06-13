<div class="container-fluid py-5 text-center">
    <h1 class="display-4 fw-bold mb-2">LISTOS PARA RECOGER</h1>
    <p class="lead text-white-50 mb-5">Acércate a caja cuando aparezca tu número.</p>
    <div class="row g-4 justify-content-center" id="readyOrders"></div>
</div>
<script>
async function loadReadyOrders() {
    const response = await fetch('<?= e(url('pedidos/listos-json')) ?>');
    const orders = await response.json();
    const container = document.getElementById('readyOrders');
    container.innerHTML = orders.length === 0 ? '<p class="text-white-50 fs-2">Sin pedidos listos</p>' : '';
    orders.forEach(order => container.insertAdjacentHTML('beforeend', `<div class="col-6 col-md-3"><div class="ready-number p-4">${order.order_number}</div></div>`));
}
loadReadyOrders();
setInterval(loadReadyOrders, 5000);
</script>
