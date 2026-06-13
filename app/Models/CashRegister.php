<?php

declare(strict_types=1);

namespace App\Models;

final class CashRegister extends BaseModel
{
    public function activeForUser(int $userId): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM cash_registers WHERE user_id = :user_id AND status = 'abierta' ORDER BY opened_at DESC LIMIT 1");
        $statement->execute(['user_id' => $userId]);
        $register = $statement->fetch();

        return $register === false ? null : $register;
    }

    public function open(int $userId, float $amount, ?string $notes): int
    {
        $code = 'CJ' . date('YmdHis');
        $statement = $this->db->prepare(
            "INSERT INTO cash_registers (code, user_id, opening_amount, status, opened_at, notes)
             VALUES (:code, :user_id, :opening_amount, 'abierta', NOW(), :notes)"
        );
        $statement->execute(['code' => $code, 'user_id' => $userId, 'opening_amount' => $amount, 'notes' => $notes]);

        return (int) $this->db->lastInsertId();
    }

    public function summary(int $registerId): array
    {
        $sales = $this->scalar('SELECT COALESCE(SUM(total), 0) FROM sales WHERE cash_register_id = :id AND status <> "anulada"', ['id' => $registerId]);
        $income = $this->scalar('SELECT COALESCE(SUM(amount), 0) FROM cash_movements WHERE cash_register_id = :id AND type = "ingreso"', ['id' => $registerId]);
        $expense = $this->scalar('SELECT COALESCE(SUM(amount), 0) FROM cash_movements WHERE cash_register_id = :id AND type = "egreso"', ['id' => $registerId]);
        $opening = $this->scalar('SELECT opening_amount FROM cash_registers WHERE id = :id', ['id' => $registerId]);

        return [
            'opening' => $opening,
            'sales' => $sales,
            'income' => $income,
            'expense' => $expense,
            'expected' => $opening + $sales + $income - $expense,
        ];
    }

    public function movements(int $registerId): array
    {
        $statement = $this->db->prepare('SELECT * FROM cash_movements WHERE cash_register_id = :id ORDER BY created_at DESC');
        $statement->execute(['id' => $registerId]);

        return $statement->fetchAll();
    }

    public function addMovement(int $registerId, string $type, string $concept, float $amount, int $userId): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO cash_movements (cash_register_id, type, concept, amount, created_by)
             VALUES (:cash_register_id, :type, :concept, :amount, :created_by)'
        );
        $statement->execute([
            'cash_register_id' => $registerId,
            'type' => $type,
            'concept' => $concept,
            'amount' => $amount,
            'created_by' => $userId,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function close(int $registerId, float $closingAmount, ?string $notes): void
    {
        $summary = $this->summary($registerId);
        $statement = $this->db->prepare(
            "UPDATE cash_registers
             SET closing_amount = :closing_amount, expected_amount = :expected_amount,
                 difference_amount = :difference_amount, status = 'cerrada', closed_at = NOW(), notes = :notes
             WHERE id = :id"
        );
        $statement->execute([
            'closing_amount' => $closingAmount,
            'expected_amount' => $summary['expected'],
            'difference_amount' => $closingAmount - $summary['expected'],
            'notes' => $notes,
            'id' => $registerId,
        ]);
    }

    public function history(): array
    {
        return $this->db->query(
            'SELECT cr.*, u.name AS user_name
             FROM cash_registers cr
             INNER JOIN users u ON u.id = cr.user_id
             ORDER BY cr.opened_at DESC
             LIMIT 50'
        )->fetchAll();
    }

    private function scalar(string $sql, array $params): float
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (float) $statement->fetchColumn();
    }
}
