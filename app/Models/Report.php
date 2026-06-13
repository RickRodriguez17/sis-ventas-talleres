<?php

declare(strict_types=1);

namespace App\Models;

final class Report extends BaseModel
{
    public function production(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            'SELECT p.name, COALESCE(SUM(pd.produced_quantity), 0) AS produced,
                    COALESCE(SUM(pd.remaining_quantity), 0) AS remaining,
                    COALESCE(SUM((pd.produced_quantity - pd.remaining_quantity) * p.price), 0) AS estimated_sales
             FROM production_details pd
             INNER JOIN products p ON p.id = pd.product_id
             WHERE pd.production_date BETWEEN :from AND :to
             GROUP BY p.id
             ORDER BY p.name'
        );
        $statement->execute(['from' => $from, 'to' => $to]);

        return $statement->fetchAll();
    }

    public function cash(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            'SELECT cr.*, u.name AS user_name
             FROM cash_registers cr
             INNER JOIN users u ON u.id = cr.user_id
             WHERE DATE(cr.opened_at) BETWEEN :from AND :to
             ORDER BY cr.opened_at DESC'
        );
        $statement->execute(['from' => $from, 'to' => $to]);

        return $statement->fetchAll();
    }
}
