<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Setting;

final class SettingController extends Controller
{
    public function edit(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('settings/edit', ['title' => 'Configuración', 'settings' => (new Setting())->current()]);
    }

    public function update(): void
    {
        Auth::requireRoles(['administrador']);

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('configuracion');
        }

        $current = (new Setting())->current();
        $businessName = trim((string) ($_POST['business_name'] ?? ''));

        if ($businessName === '') {
            Flash::set('error', 'El nombre del negocio es obligatorio.');
            $this->redirect('configuracion');
        }

        (new Setting())->update([
            'business_name' => $businessName,
            'logo' => $this->saveUpload('logo', 'settings', $current['logo'] ?? null),
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'phone' => trim((string) ($_POST['phone'] ?? '')) ?: null,
            'currency' => trim((string) ($_POST['currency'] ?? 'Bs')) ?: 'Bs',
            'ticket_footer' => trim((string) ($_POST['ticket_footer'] ?? '')) ?: null,
        ]);
        (new AuditLog())->register(Auth::id(), 'actualizar', 'settings');
        Flash::set('success', 'Configuración actualizada.');
        $this->redirect('configuracion');
    }
}
