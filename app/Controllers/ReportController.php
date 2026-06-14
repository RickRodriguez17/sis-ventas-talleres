<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Report;
use App\Models\Sale;
use App\Models\Setting;

final class ReportController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador']);
        [$from, $to] = $this->dates();
        $saleModel = new Sale();
        $reportModel = new Report();
        $this->render('reports/index', [
            'title' => 'Reportes',
            'from' => $from,
            'to' => $to,
            'sales' => $saleModel->filtered($from, $to),
            'summary' => $saleModel->reportSummary($from, $to),
            'production' => $reportModel->production($from, $to),
            'cash' => $reportModel->cash($from, $to),
            'settings' => (new Setting())->current(),
        ]);
    }

    public function export(): void
    {
        Auth::requireRoles(['administrador']);
        [$from, $to] = $this->dates();
        $sales = (new Sale())->filtered($from, $to);

        $this->render('reports/pdf', ['title' => 'Reporte imprimible', 'sales' => $sales, 'from' => $from, 'to' => $to, 'settings' => (new Setting())->current()], 'print');
    }

    private function dates(): array
    {
        $from = (string) ($_GET['from'] ?? date('Y-m-01'));
        $to = (string) ($_GET['to'] ?? date('Y-m-d'));

        return [$from, $to];
    }
}
