<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->render('errors/404', ['title' => 'Página no encontrada']);
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('errors/403', ['title' => 'Acceso restringido']);
    }
}
