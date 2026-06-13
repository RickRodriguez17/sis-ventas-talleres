<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AdminUser;
use App\Models\AuditLog;

final class UserController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('users/index', ['title' => 'Usuarios', 'users' => (new AdminUser())->all()]);
    }

    public function create(): void
    {
        Auth::requireRoles(['administrador']);
        $this->renderForm('Nuevo usuario', null);
    }

    public function store(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $password = (string) ($_POST['password'] ?? '');

        if ($password === '') {
            Flash::set('error', 'La contraseña es obligatoria.');
            $this->redirect('usuarios/crear');
        }

        $data = $this->payload();
        $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        $id = (new AdminUser())->create($data);
        (new AuditLog())->register(Auth::id(), 'crear', 'users', $id, $data['email']);
        Flash::set('success', 'Usuario creado.');
        $this->redirect('usuarios');
    }

    public function edit(): void
    {
        Auth::requireRoles(['administrador']);
        $user = (new AdminUser())->find((int) ($_GET['id'] ?? 0));

        if ($user === null) {
            Flash::set('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
        }

        $this->renderForm('Editar usuario', $user);
    }

    public function update(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $id = (int) ($_GET['id'] ?? 0);
        (new AdminUser())->update($id, $this->payload(), (string) ($_POST['password'] ?? ''));
        (new AuditLog())->register(Auth::id(), 'actualizar', 'users', $id);
        Flash::set('success', 'Usuario actualizado.');
        $this->redirect('usuarios');
    }

    public function toggle(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();

        if ((new AdminUser())->toggle((int) ($_POST['id'] ?? 0), (int) Auth::id())) {
            Flash::set('success', 'Estado del usuario actualizado.');
        } else {
            Flash::set('error', 'No puedes desactivar tu propio usuario.');
        }

        $this->redirect('usuarios');
    }

    private function renderForm(string $title, ?array $user): void
    {
        $this->render('users/form', ['title' => $title, 'userItem' => $user, 'roles' => (new AdminUser())->roles()]);
    }

    private function payload(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Nombre y correo válido son obligatorios.');
            redirect_back('usuarios');
        }

        return [
            'role_id' => (int) ($_POST['role_id'] ?? 2),
            'name' => $name,
            'email' => $email,
            'phone' => trim((string) ($_POST['phone'] ?? '')) ?: null,
            'status' => isset($_POST['status']) ? 1 : 0,
        ];
    }

    private function validateToken(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('usuarios');
        }
    }
}
