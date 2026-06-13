<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'FastFood Ventas',
    'base_url' => rtrim((string) (getenv('APP_URL') ?: ''), '/'),
    'session_name' => getenv('SESSION_NAME') ?: 'sis_ventas_session',
];
