<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends BaseModel
{
    public function current(): array
    {
        $setting = $this->db->query('SELECT * FROM settings ORDER BY id LIMIT 1')->fetch();

        return $setting === false ? [] : $setting;
    }

    public function update(array $data): void
    {
        $current = $this->current();

        if ($current === []) {
            $statement = $this->db->prepare(
                'INSERT INTO settings (business_name, logo, address, phone, currency, ticket_footer)
                 VALUES (:business_name, :logo, :address, :phone, :currency, :ticket_footer)'
            );
            $statement->execute($data);
            return;
        }

        $data['id'] = (int) $current['id'];
        $statement = $this->db->prepare(
            'UPDATE settings
             SET business_name = :business_name, logo = :logo, address = :address, phone = :phone,
                 currency = :currency, ticket_footer = :ticket_footer
             WHERE id = :id'
        );
        $statement->execute($data);
    }
}
