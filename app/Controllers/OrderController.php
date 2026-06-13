<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\Sale;

final class OrderController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $status = (string) ($_GET['status'] ?? '');
        $this->render('orders/index', [
            'title' => 'Pedidos',
            'orders' => (new Sale())->orders($status),
            'status' => $status,
        ]);
    }

    public function updateStatus(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('pedidos');
        }

        $status = (string) ($_POST['status'] ?? '');

        if (!in_array($status, ['pendiente', 'en_preparacion', 'listo', 'entregado'], true)) {
            Flash::set('error', 'Estado inválido.');
            $this->redirect('pedidos');
        }

        (new Sale())->updateStatus((int) ($_POST['id'] ?? 0), $status, (int) Auth::id());
        Flash::set('success', 'Estado del pedido actualizado.');
        $this->redirect('pedidos');
    }

    public function readyScreen(): void
    {
        $this->render('orders/ready', ['title' => 'Pedidos listos'], 'ready');
    }

    public function readyJson(): void
    {
        header('Content-Type: application/json');
        echo json_encode((new Sale())->readyOrders(), JSON_THROW_ON_ERROR);
    }
}
