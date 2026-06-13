<?php

declare(strict_types=1);

namespace App\Models;

final class Product extends BaseModel
{
    public function all(bool $activeOnly = false): array
    {
        $sql = "SELECT p.*, c.name AS category_name,
                COALESCE(SUM(CASE WHEN pd.production_date = CURDATE() THEN pd.remaining_quantity ELSE 0 END), 0) AS stock_today
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN production_details pd ON pd.product_id = p.id";
        $sql .= $activeOnly ? " WHERE p.status = 'activo' AND c.status = 1" : '';
        $sql .= ' GROUP BY p.id ORDER BY p.name';

        return $this->db->query($sql)->fetchAll();
    }

    public function availableForSale(): array
    {
        $products = $this->all(true);

        foreach ($products as $index => $product) {
            $products[$index]['available_quantity'] = $this->availableQuantity((int) $product['id']);
        }

        return $products;
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    public function comboItems(int $productId): array
    {
        $statement = $this->db->prepare(
            'SELECT ci.*, p.name AS product_name
             FROM combo_items ci
             INNER JOIN products p ON p.id = ci.product_id
             WHERE ci.combo_product_id = :product_id
             ORDER BY p.name'
        );
        $statement->execute(['product_id' => $productId]);

        return $statement->fetchAll();
    }

    public function create(array $data, array $comboItems = []): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO products (category_id, name, slug, price, image, description, is_combo, status)
             VALUES (:category_id, :name, :slug, :price, :image, :description, :is_combo, :status)'
        );
        $statement->execute($data);
        $id = (int) $this->db->lastInsertId();
        $this->syncComboItems($id, $comboItems);

        return $id;
    }

    public function update(int $id, array $data, array $comboItems = []): void
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE products
             SET category_id = :category_id, name = :name, slug = :slug, price = :price, image = :image,
                 description = :description, is_combo = :is_combo, status = :status
             WHERE id = :id'
        );
        $statement->execute($data);
        $this->syncComboItems($id, $comboItems);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM sale_details WHERE product_id = :id');
        $statement->execute(['id' => $id]);

        if ((int) $statement->fetchColumn() > 0) {
            return false;
        }

        $this->db->prepare('DELETE FROM combo_items WHERE combo_product_id = :id OR product_id = :id')->execute(['id' => $id]);
        $this->db->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);

        return true;
    }

    public function toggle(int $id): void
    {
        $statement = $this->db->prepare("UPDATE products SET status = IF(status = 'activo', 'inactivo', 'activo') WHERE id = :id");
        $statement->execute(['id' => $id]);
    }

    public function availableQuantity(int $productId): int
    {
        $product = $this->find($productId);

        if ($product === null) {
            return 0;
        }

        if ((int) $product['is_combo'] === 1) {
            $items = $this->comboItems($productId);

            if ($items === []) {
                return 0;
            }

            $available = [];

            foreach ($items as $item) {
                $componentStock = $this->simpleProductStock((int) $item['product_id']);
                $available[] = intdiv($componentStock, max(1, (int) $item['quantity']));
            }

            return min($available);
        }

        return $this->simpleProductStock($productId);
    }

    private function simpleProductStock(int $productId): int
    {
        $statement = $this->db->prepare(
            'SELECT COALESCE(SUM(remaining_quantity), 0)
             FROM production_details
             WHERE product_id = :product_id AND production_date = CURDATE()'
        );
        $statement->execute(['product_id' => $productId]);

        return (int) $statement->fetchColumn();
    }

    private function syncComboItems(int $comboProductId, array $items): void
    {
        $this->db->prepare('DELETE FROM combo_items WHERE combo_product_id = :combo_product_id')->execute(['combo_product_id' => $comboProductId]);

        foreach ($items as $productId => $quantity) {
            $quantity = (int) $quantity;
            $productId = (int) $productId;

            if ($productId <= 0 || $quantity <= 0 || $productId === $comboProductId) {
                continue;
            }

            $statement = $this->db->prepare(
                'INSERT INTO combo_items (combo_product_id, product_id, quantity)
                 VALUES (:combo_product_id, :product_id, :quantity)'
            );
            $statement->execute([
                'combo_product_id' => $comboProductId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
    }
}
