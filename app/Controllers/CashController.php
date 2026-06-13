<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\CashRegister;

final class CashController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $model = new CashRegister();
        $active = $model->activeForUser((int) Auth::id());
        $summary = $active === null ? null : $model->summary((int) $active['id']);
        $movements = $active === null ? [] : $model->movements((int) $active['id']);
        $this->render('cash/index', [
            'title' => 'Caja',
            'active' => $active,
            'summary' => $summary,
            'movements' => $movements,
            'history' => $model->history(),
        ]);
    }

    public function open(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $this->validateToken();
        $model = new CashRegister();

        if ($model->activeForUser((int) Auth::id()) !== null) {
            Flash::set('error', 'Ya tienes una caja abierta.');
            $this->redirect('caja');
        }

        $id = $model->open((int) Auth::id(), max(0, (float) ($_POST['opening_amount'] ?? 0)), trim((string) ($_POST['notes'] ?? '')) ?: null);
        (new AuditLog())->register(Auth::id(), 'abrir', 'cash_registers', $id);
        Flash::set('success', 'Caja abierta.');
        $this->redirect('caja');
    }

    public function movement(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $this->validateToken();
        $model = new CashRegister();
        $active = $model->activeForUser((int) Auth::id());

        if ($active === null) {
            Flash::set('error', 'Abre caja antes de registrar movimientos.');
            $this->redirect('caja');
        }

        $type = (string) ($_POST['type'] ?? 'ingreso');
        $concept = trim((string) ($_POST['concept'] ?? ''));
        $amount = max(0, (float) ($_POST['amount'] ?? 0));

        if (!in_array($type, ['ingreso', 'egreso'], true) || $concept === '' || $amount <= 0) {
            Flash::set('error', 'Movimiento inválido.');
            $this->redirect('caja');
        }

        $model->addMovement((int) $active['id'], $type, $concept, $amount, (int) Auth::id());
        Flash::set('success', 'Movimiento registrado.');
        $this->redirect('caja');
    }

    public function close(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $this->validateToken();
        $model = new CashRegister();
        $active = $model->activeForUser((int) Auth::id());

        if ($active === null) {
            Flash::set('error', 'No tienes caja abierta.');
            $this->redirect('caja');
        }

        $model->close((int) $active['id'], max(0, (float) ($_POST['closing_amount'] ?? 0)), trim((string) ($_POST['notes'] ?? '')) ?: null);
        (new AuditLog())->register(Auth::id(), 'cerrar', 'cash_registers', (int) $active['id']);
        Flash::set('success', 'Caja cerrada.');
        $this->redirect('caja');
    }

    private function validateToken(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('caja');
        }
    }
}
