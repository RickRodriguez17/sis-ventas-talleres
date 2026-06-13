<?php

declare(strict_types=1);

namespace App\Models;

final class AuditLog extends BaseModel
{
    public function register(?int $userId, string $action, ?string $entity = null, ?int $entityId = null, ?string $description = null): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO audit_logs (user_id, action, entity, entity_id, description, ip_address, user_agent)
             VALUES (:user_id, :action, :entity, :entity_id, :description, :ip_address, :user_agent)'
        );
        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);
    }

    public function latest(int $limit = 10): array
    {
        $statement = $this->db->prepare(
            'SELECT a.*, u.name AS user_name
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
