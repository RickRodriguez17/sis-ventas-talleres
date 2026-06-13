<?php

declare(strict_types=1);

namespace App\Models;

final class Production extends BaseModel
{
    public function todayAvailability(): array
    {
        return $this->availabilityByDate(date('Y-m-d'));
    }

    public function availabilityByDate(string $date): array
    {
        $statement = $this->db->prepare(
            "SELECT p.id, p.name, p.price, p.status, c.name AS category_name,
                    COALESCE(SUM(pd.produced_quantity), 0) AS produced_quantity,
                    COALESCE(SUM(pd.remaining_quantity), 0) AS remaining_quantity,
                    MAX(COALESCE(pd.low_stock_alert, 5)) AS low_stock_alert
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN production_details pd ON pd.product_id = p.id AND pd.production_date = :date
             WHERE p.status = 'activo' AND p.is_combo = 0
             GROUP BY p.id
             ORDER BY c.name, p.name"
        );
        $statement->execute(['date' => $date]);

        return $statement->fetchAll();
    }

    public function batches(): array
    {
        return $this->db->query(
            'SELECT pb.*, u.name AS user_name, COUNT(pd.id) AS items_count,
                    COALESCE(SUM(pd.produced_quantity), 0) AS total_produced,
                    COALESCE(SUM(pd.remaining_quantity), 0) AS total_remaining
             FROM production_batches pb
             INNER JOIN users u ON u.id = pb.user_id
             LEFT JOIN production_details pd ON pd.batch_id = pb.id
             GROUP BY pb.id
             ORDER BY pb.production_date DESC, pb.created_at DESC'
        )->fetchAll();
    }

    public function createBatch(string $date, int $userId, ?string $notes, array $items): int
    {
        $this->db->beginTransaction();

        try {
            $statement = $this->db->prepare('INSERT INTO production_batches (production_date, user_id, notes) VALUES (:production_date, :user_id, :notes)');
            $statement->execute(['production_date' => $date, 'user_id' => $userId, 'notes' => $notes]);
            $batchId = (int) $this->db->lastInsertId();

            foreach ($items as $productId => $item) {
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($quantity <= 0) {
                    continue;
                }

                $statement = $this->db->prepare(
                    'INSERT INTO production_details
                        (batch_id, product_id, production_date, produced_quantity, remaining_quantity, low_stock_alert, unit_cost)
                     VALUES (:batch_id, :product_id, :production_date, :produced_quantity, :remaining_quantity, :low_stock_alert, :unit_cost)'
                );
                $statement->execute([
                    'batch_id' => $batchId,
                    'product_id' => (int) $productId,
                    'production_date' => $date,
                    'produced_quantity' => $quantity,
                    'remaining_quantity' => $quantity,
                    'low_stock_alert' => max(1, (int) ($item['low_stock_alert'] ?? 5)),
                    'unit_cost' => max(0, (float) ($item['unit_cost'] ?? 0)),
                ]);
            }

            $this->db->commit();

            return $batchId;
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }
}
