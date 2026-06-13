<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;

final class SaleController extends Controller
{
    public function pos(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $activeCash = (new CashRegister())->activeForUser((int) Auth::id());
        $this->render('sales/pos', [
            'title' => 'POS / Ventas',
            'products' => (new Product())->availableForSale(),
            'activeCash' => $activeCash,
        ]);
    }

    public function store(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('ventas');
        }

        $activeCash = (new CashRegister())->activeForUser((int) Auth::id());

        if ($activeCash === null) {
            Flash::set('error', 'Debes abrir caja antes de vender.');
            $this->redirect('caja');
        }

        try {
            $saleId = (new Sale())->create([
                'user_id' => Auth::id(),
                'cash_register_id' => (int) $activeCash['id'],
                'discount_type' => (string) ($_POST['discount_type'] ?? 'none'),
                'discount_value' => (float) ($_POST['discount_value'] ?? 0),
                'paid_amount' => (float) ($_POST['paid_amount'] ?? 0),
                'payment_method' => (string) ($_POST['payment_method'] ?? 'efectivo'),
                'observations' => trim((string) ($_POST['observations'] ?? '')) ?: null,
            ], $_POST['items'] ?? []);
            (new AuditLog())->register(Auth::id(), 'crear', 'sales', $saleId);
            Flash::set('success', 'Venta registrada.');
            $this->redirect('ventas/ticket&id=' . $saleId);
        } catch (\RuntimeException $exception) {
            Flash::set('error', $exception->getMessage());
            $this->redirect('ventas');
        }
    }

    public function ticket(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $sale = (new Sale())->findWithDetails((int) ($_GET['id'] ?? 0));

        if ($sale === null) {
            Flash::set('error', 'Venta no encontrada.');
            $this->redirect('ventas');
        }

        $this->render('sales/ticket', [
            'title' => 'Ticket ' . $sale['order_number'],
            'sale' => $sale,
            'settings' => (new Setting())->current(),
        ]);
    }

    public function void(): void
    {
        Auth::requireRoles(['administrador']);

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('pedidos');
        }

        try {
            (new Sale())->void((int) ($_POST['id'] ?? 0), (int) Auth::id(), trim((string) ($_POST['reason'] ?? 'Anulación administrativa')));
            (new AuditLog())->register(Auth::id(), 'anular', 'sales', (int) ($_POST['id'] ?? 0));
            Flash::set('success', 'Venta anulada y producción restaurada.');
        } catch (\RuntimeException $exception) {
            Flash::set('error', $exception->getMessage());
        }

        $this->redirect('pedidos');
    }
}
