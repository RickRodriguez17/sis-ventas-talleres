<?php

declare(strict_types=1);

namespace App\Models;

final class AdminUser extends BaseModel
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             ORDER BY u.name'
        )->fetchAll();
    }

    public function roles(): array
    {
        return $this->db->query('SELECT * FROM roles ORDER BY name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (role_id, name, email, password, phone, status)
             VALUES (:role_id, :name, :email, :password, :phone, :status)'
        );
        $statement->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, ?string $password): void
    {
        $data['id'] = $id;

        if ($password !== null && $password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $statement = $this->db->prepare(
                'UPDATE users
                 SET role_id = :role_id, name = :name, email = :email, password = :password, phone = :phone, status = :status
                 WHERE id = :id'
            );
            $statement->execute($data);
            return;
        }

        $statement = $this->db->prepare(
            'UPDATE users SET role_id = :role_id, name = :name, email = :email, phone = :phone, status = :status WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function toggle(int $id, int $currentUserId): bool
    {
        if ($id === $currentUserId) {
            return false;
        }

        $statement = $this->db->prepare('UPDATE users SET status = IF(status = 1, 0, 1) WHERE id = :id');
        $statement->execute(['id' => $id]);

        return true;
    }
}
