<?php

declare(strict_types=1);

namespace App\Models;

final class Category extends BaseModel
{
    public function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS products_count FROM categories c LEFT JOIN products p ON p.category_id = c.id';
        $sql .= $activeOnly ? ' WHERE c.status = 1' : '';
        $sql .= ' GROUP BY c.id ORDER BY c.name';

        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $statement->execute(['id' => $id]);
        $category = $statement->fetch();

        return $category === false ? null : $category;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO categories (name, slug, description, status)
             VALUES (:name, :slug, :description, :status)'
        );
        $statement->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = $this->db->prepare(
            'UPDATE categories SET name = :name, slug = :slug, description = :description, status = :status WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $statement->execute(['id' => $id]);

        if ((int) $statement->fetchColumn() > 0) {
            return false;
        }

        $statement = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $statement->execute(['id' => $id]);

        return true;
    }

    public function toggle(int $id): void
    {
        $statement = $this->db->prepare('UPDATE categories SET status = IF(status = 1, 0, 1) WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
