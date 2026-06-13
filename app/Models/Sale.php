<?php

declare(strict_types=1);

namespace App\Models;

final class Sale extends BaseModel
{
    public function create(array $sale, array $items): int
    {
        $this->db->beginTransaction();

        try {
            $productModel = new Product();
            $subtotal = 0.0;
            $details = [];

            foreach ($items as $productId => $quantity) {
                $productId = (int) $productId;
                $quantity = (int) $quantity;

                if ($productId <= 0 || $quantity <= 0) {
                    continue;
                }

                $product = $productModel->find($productId);

                if ($product === null || $product['status'] !== 'activo') {
                    throw new \RuntimeException('Producto no disponible.');
                }

                if ($productModel->availableQuantity($productId) < $quantity) {
                    throw new \RuntimeException('Stock insuficiente para ' . $product['name'] . '.');
                }

                $lineTotal = (float) $product['price'] * $quantity;
                $subtotal += $lineTotal;
                $details[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product['price'],
                    'total' => $lineTotal,
                ];
            }

            if ($details === []) {
                throw new \RuntimeException('Agrega al menos un producto al pedido.');
            }

            $discountType = (string) ($sale['discount_type'] ?? 'none');
            $discountValue = max(0, (float) ($sale['discount_value'] ?? 0));
            $discountTotal = $discountType === 'percentage' ? ($subtotal * min($discountValue, 100) / 100) : ($discountType === 'fixed' ? min($discountValue, $subtotal) : 0.0);
            $total = max(0, $subtotal - $discountTotal);
            $paidAmount = max(0, (float) ($sale['paid_amount'] ?? 0));
            $changeAmount = max(0, $paidAmount - $total);
            $orderNumber = $this->nextOrderNumber();

            $statement = $this->db->prepare(
                "INSERT INTO sales
                    (order_number, user_id, cash_register_id, subtotal, discount_type, discount_value, discount_total, total,
                     paid_amount, change_amount, payment_method, observations, order_status, status)
                 VALUES
                    (:order_number, :user_id, :cash_register_id, :subtotal, :discount_type, :discount_value, :discount_total, :total,
                     :paid_amount, :change_amount, :payment_method, :observations, 'pendiente', 'activa')"
            );
            $statement->execute([
                'order_number' => $orderNumber,
                'user_id' => (int) $sale['user_id'],
                'cash_register_id' => $sale['cash_register_id'],
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_total' => $discountTotal,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => (string) $sale['payment_method'],
                'observations' => $sale['observations'] ?? null,
            ]);
            $saleId = (int) $this->db->lastInsertId();

            foreach ($details as $detail) {
                $product = $detail['product'];
                $statement = $this->db->prepare(
                    'INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, total)
                     VALUES (:sale_id, :product_id, :quantity, :unit_price, :total)'
                );
                $statement->execute([
                    'sale_id' => $saleId,
                    'product_id' => (int) $product['id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total' => $detail['total'],
                ]);
                $this->discountProduction((int) $product['id'], $detail['quantity']);
            }

            $this->db->prepare(
                "INSERT INTO order_status_history (sale_id, previous_status, new_status, changed_by)
                 VALUES (:sale_id, NULL, 'pendiente', :changed_by)"
            )->execute(['sale_id' => $saleId, 'changed_by' => (int) $sale['user_id']]);
            $this->db->commit();

            return $saleId;
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function findWithDetails(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT s.*, u.name AS user_name, cr.code AS cash_code
             FROM sales s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN cash_registers cr ON cr.id = s.cash_register_id
             WHERE s.id = :id'
        );
        $statement->execute(['id' => $id]);
        $sale = $statement->fetch();

        if ($sale === false) {
            return null;
        }

        $statement = $this->db->prepare(
            'SELECT sd.*, p.name AS product_name
             FROM sale_details sd
             INNER JOIN products p ON p.id = sd.product_id
             WHERE sd.sale_id = :sale_id
             ORDER BY sd.id'
        );
        $statement->execute(['sale_id' => $id]);
        $sale['details'] = $statement->fetchAll();

        return $sale;
    }

    public function orders(?string $status = null): array
    {
        $sql = 'SELECT s.*, u.name AS user_name FROM sales s INNER JOIN users u ON u.id = s.user_id WHERE s.status <> "anulada"';
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= ' AND s.order_status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY s.created_at DESC LIMIT 100';
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public function readyOrders(): array
    {
        return $this->orders('listo');
    }

    public function updateStatus(int $saleId, string $newStatus, int $userId): void
    {
        $sale = $this->findWithDetails($saleId);

        if ($sale === null) {
            throw new \RuntimeException('Pedido no encontrado.');
        }

        $statement = $this->db->prepare('UPDATE sales SET order_status = :status WHERE id = :id');
        $statement->execute(['status' => $newStatus, 'id' => $saleId]);
        $statement = $this->db->prepare(
            'INSERT INTO order_status_history (sale_id, previous_status, new_status, changed_by)
             VALUES (:sale_id, :previous_status, :new_status, :changed_by)'
        );
        $statement->execute([
            'sale_id' => $saleId,
            'previous_status' => $sale['order_status'],
            'new_status' => $newStatus,
            'changed_by' => $userId,
        ]);
    }

    public function void(int $saleId, int $userId, string $reason): void
    {
        $this->db->beginTransaction();

        try {
            $sale = $this->findWithDetails($saleId);

            if ($sale === null || $sale['status'] === 'anulada') {
                throw new \RuntimeException('Venta no encontrada o ya anulada.');
            }

            foreach ($sale['details'] as $detail) {
                $this->restoreProduction((int) $detail['product_id'], (int) $detail['quantity']);
            }

            $this->db->prepare("UPDATE sales SET status = 'anulada' WHERE id = :id")->execute(['id' => $saleId]);
            $this->db->prepare('INSERT INTO voided_sales (sale_id, voided_by, reason) VALUES (:sale_id, :voided_by, :reason)')
                ->execute(['sale_id' => $saleId, 'voided_by' => $userId, 'reason' => $reason]);
            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }

    public function filtered(string $from, string $to): array
    {
        $statement = $this->db->prepare(
            'SELECT s.*, u.name AS user_name
             FROM sales s
             INNER JOIN users u ON u.id = s.user_id
             WHERE DATE(s.created_at) BETWEEN :from AND :to
             ORDER BY s.created_at DESC'
        );
        $statement->execute(['from' => $from, 'to' => $to]);

        return $statement->fetchAll();
    }

    public function reportSummary(string $from, string $to): array
    {
        $summary = [];
        $statement = $this->db->prepare('SELECT COALESCE(SUM(total), 0), COUNT(*) FROM sales WHERE status <> "anulada" AND DATE(created_at) BETWEEN :from AND :to');
        $statement->execute(['from' => $from, 'to' => $to]);
        [$summary['sales_total'], $summary['sales_count']] = array_map('floatval', $statement->fetch(\PDO::FETCH_NUM));

        $statement = $this->db->prepare(
            'SELECT p.name, COALESCE(SUM(sd.quantity), 0) AS quantity, COALESCE(SUM(sd.total), 0) AS total
             FROM sale_details sd
             INNER JOIN sales s ON s.id = sd.sale_id
             INNER JOIN products p ON p.id = sd.product_id
             WHERE s.status <> "anulada" AND DATE(s.created_at) BETWEEN :from AND :to
             GROUP BY p.id
             ORDER BY quantity DESC'
        );
        $statement->execute(['from' => $from, 'to' => $to]);
        $summary['products'] = $statement->fetchAll();

        $statement = $this->db->prepare(
            'SELECT payment_method, COALESCE(SUM(total), 0) AS total, COUNT(*) AS count
             FROM sales
             WHERE status <> "anulada" AND DATE(created_at) BETWEEN :from AND :to
             GROUP BY payment_method'
        );
        $statement->execute(['from' => $from, 'to' => $to]);
        $summary['payments'] = $statement->fetchAll();

        return $summary;
    }

    private function nextOrderNumber(): string
    {
        $statement = $this->db->query('SELECT COALESCE(MAX(id), 0) + 1 FROM sales');
        $next = (int) $statement->fetchColumn();

        return 'A' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function discountProduction(int $productId, int $quantity): void
    {
        $productModel = new Product();
        $product = $productModel->find($productId);

        if ($product !== null && (int) $product['is_combo'] === 1) {
            foreach ($productModel->comboItems($productId) as $item) {
                $this->discountSimpleProduct((int) $item['product_id'], $quantity * (int) $item['quantity']);
            }

            return;
        }

        $this->discountSimpleProduct($productId, $quantity);
    }

    private function discountSimpleProduct(int $productId, int $quantity): void
    {
        $statement = $this->db->prepare(
            'SELECT id, remaining_quantity
             FROM production_details
             WHERE product_id = :product_id AND production_date = CURDATE() AND remaining_quantity > 0
             ORDER BY id'
        );
        $statement->execute(['product_id' => $productId]);
        $pending = $quantity;

        foreach ($statement->fetchAll() as $row) {
            if ($pending <= 0) {
                break;
            }

            $discount = min($pending, (int) $row['remaining_quantity']);
            $this->db->prepare('UPDATE production_details SET remaining_quantity = remaining_quantity - :discount WHERE id = :id')
                ->execute(['discount' => $discount, 'id' => (int) $row['id']]);
            $pending -= $discount;
        }

        if ($pending > 0) {
            throw new \RuntimeException('Stock insuficiente durante el descuento de producción.');
        }
    }

    private function restoreProduction(int $productId, int $quantity): void
    {
        $productModel = new Product();
        $product = $productModel->find($productId);

        if ($product !== null && (int) $product['is_combo'] === 1) {
            foreach ($productModel->comboItems($productId) as $item) {
                $this->restoreSimpleProduct((int) $item['product_id'], $quantity * (int) $item['quantity']);
            }

            return;
        }

        $this->restoreSimpleProduct($productId, $quantity);
    }

    private function restoreSimpleProduct(int $productId, int $quantity): void
    {
        $statement = $this->db->prepare(
            'SELECT id FROM production_details
             WHERE product_id = :product_id AND production_date = CURDATE()
             ORDER BY id DESC LIMIT 1'
        );
        $statement->execute(['product_id' => $productId]);
        $detailId = (int) $statement->fetchColumn();

        if ($detailId > 0) {
            $this->db->prepare('UPDATE production_details SET remaining_quantity = remaining_quantity + :quantity WHERE id = :id')
                ->execute(['quantity' => $quantity, 'id' => $detailId]);
        }
    }
}
