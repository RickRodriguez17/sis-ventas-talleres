<?php

declare(strict_types=1);

namespace App\Models;

final class User extends BaseModel
{
    public function findActiveByEmail(string $email): ?array
    {
        $statement = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.password, u.status, r.name AS role_name, r.slug AS role_slug
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email AND u.status = 1
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function updateLastLogin(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
