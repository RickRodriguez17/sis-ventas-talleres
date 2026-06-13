<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Production;

final class ProductionController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $this->render('production/index', ['title' => 'Producción', 'items' => (new Production())->todayAvailability()]);
    }

    public function create(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('production/create', ['title' => 'Registrar producción', 'products' => (new Product())->all(true)]);
    }

    public function store(): void
    {
        Auth::requireRoles(['administrador']);

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('produccion');
        }

        $date = (string) ($_POST['production_date'] ?? today());
        $id = (new Production())->createBatch($date, (int) Auth::id(), trim((string) ($_POST['notes'] ?? '')) ?: null, $_POST['items'] ?? []);
        (new AuditLog())->register(Auth::id(), 'crear', 'production_batches', $id, 'Producción ' . $date);
        Flash::set('success', 'Producción registrada.');
        $this->redirect('produccion');
    }

    public function history(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('production/history', ['title' => 'Historial de producción', 'batches' => (new Production())->batches()]);
    }
}
