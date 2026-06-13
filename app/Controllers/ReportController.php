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
        $format = (string) ($_GET['format'] ?? 'csv');
        $sales = (new Sale())->filtered($from, $to);

        if ($format === 'xls') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename=reporte-ventas.xls');
            echo "<table><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th>Método</th><th>Estado</th></tr>";
            foreach ($sales as $sale) {
                echo '<tr><td>' . e($sale['order_number']) . '</td><td>' . e($sale['created_at']) . '</td><td>' . e((string) $sale['total']) . '</td><td>' . e($sale['payment_method']) . '</td><td>' . e($sale['status']) . '</td></tr>';
            }
            echo '</table>';
            return;
        }

        if ($format === 'pdf') {
            $this->render('reports/pdf', ['title' => 'Reporte imprimible', 'sales' => $sales, 'from' => $from, 'to' => $to, 'settings' => (new Setting())->current()], 'print');
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte-ventas.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Pedido', 'Fecha', 'Total', 'Metodo', 'Estado']);

        foreach ($sales as $sale) {
            fputcsv($output, [$sale['order_number'], $sale['created_at'], $sale['total'], $sale['payment_method'], $sale['status']]);
        }
    }

    private function dates(): array
    {
        $from = (string) ($_GET['from'] ?? date('Y-m-01'));
        $to = (string) ($_GET['to'] ?? date('Y-m-d'));

        return [$from, $to];
    }
}
