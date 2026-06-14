<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\DashboardStats;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();

        $model = new DashboardStats();
        $settings = $model->settings();
        $stats = Auth::isAdmin() ? $model->admin() : $model->cashier((int) Auth::id());

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }
}
