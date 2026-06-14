<?php

declare(strict_types=1);

namespace App\Models;

final class DashboardStats extends BaseModel
{
    public function admin(): array
    {
        return [
            'sales_today' => $this->scalarFloat("SELECT COALESCE(SUM(total), 0) FROM sales WHERE DATE(created_at) = CURDATE() AND status <> 'anulada'"),
            'sales_month' => $this->scalarFloat("SELECT COALESCE(SUM(total), 0) FROM sales WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) AND status <> 'anulada'"),
            'products_sold_today' => $this->scalarInt("SELECT COALESCE(SUM(sd.quantity), 0) FROM sale_details sd INNER JOIN sales s ON s.id = sd.sale_id WHERE DATE(s.created_at) = CURDATE() AND s.status <> 'anulada'"),
            'remaining_production' => $this->scalarInt("SELECT COALESCE(SUM(remaining_quantity), 0) FROM production_details WHERE production_date = CURDATE()"),
            'exhausted_products' => $this->scalarInt("SELECT COUNT(*) FROM production_details WHERE production_date = CURDATE() AND remaining_quantity = 0"),
            'orders_total' => $this->scalarInt("SELECT COUNT(*) FROM sales WHERE DATE(created_at) = CURDATE() AND status <> 'anulada'"),
        ];
    }

    public function cashier(int $userId): array
    {
        $cashRegister = $this->activeCashRegister($userId);
        $cashRegisterId = $cashRegister === null ? 0 : (int) $cashRegister['id'];

        return [
            'cash_open' => $cashRegister !== null,
            'cash_code' => $cashRegister['code'] ?? null,
            'opening_amount' => $cashRegister === null ? 0.0 : (float) $cashRegister['opening_amount'],
            'sales_shift' => $this->scalarFloat('SELECT COALESCE(SUM(total), 0) FROM sales WHERE cash_register_id = :cash_register_id AND status <> "anulada"', ['cash_register_id' => $cashRegisterId]),
            'orders_shift' => $this->scalarInt('SELECT COUNT(*) FROM sales WHERE cash_register_id = :cash_register_id AND status <> "anulada"', ['cash_register_id' => $cashRegisterId]),
            'products_available' => $this->scalarInt('SELECT COUNT(DISTINCT product_id) FROM production_details WHERE production_date = CURDATE() AND remaining_quantity > 0'),
            'low_stock' => $this->scalarInt('SELECT COUNT(*) FROM production_details WHERE production_date = CURDATE() AND remaining_quantity > 0 AND remaining_quantity <= low_stock_alert'),
        ];
    }

    public function settings(): array
    {
        $statement = $this->db->query('SELECT business_name, currency, logo FROM settings ORDER BY id LIMIT 1');
        $settings = $statement->fetch();

        return $settings === false ? ['business_name' => 'FastFood Ventas', 'currency' => 'Bs', 'logo' => null] : $settings;
    }

    private function activeCashRegister(int $userId): ?array
    {
        $statement = $this->db->prepare(
            "SELECT id, code, opening_amount
             FROM cash_registers
             WHERE user_id = :user_id AND status = 'abierta'
             ORDER BY opened_at DESC
             LIMIT 1"
        );
        $statement->execute(['user_id' => $userId]);
        $cashRegister = $statement->fetch();

        return $cashRegister === false ? null : $cashRegister;
    }

    private function scalarFloat(string $sql, array $params = []): float
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (float) $statement->fetchColumn();
    }

    private function scalarInt(string $sql, array $params = []): int
    {
        return (int) $this->scalarFloat($sql, $params);
    }
}
