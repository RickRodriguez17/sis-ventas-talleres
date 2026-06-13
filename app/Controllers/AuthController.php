<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        Auth::requireGuest();
        $this->render('auth/login', ['title' => 'Iniciar sesión'], 'guest');
    }

    public function login(): void
    {
        Auth::requireGuest();

        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró. Intenta nuevamente.');
            $this->redirect('login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            Flash::set('error', 'Ingresa tu correo y contraseña.');
            $this->redirect('login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !Auth::attempt($email, $password)) {
            Flash::set('error', 'Credenciales inválidas o usuario inactivo.');
            $this->redirect('login');
        }

        Flash::set('success', 'Bienvenido al sistema.');
        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'No se pudo cerrar la sesión.');
            $this->redirect('dashboard');
        }

        Auth::logout();
        session_start();
        Flash::set('success', 'Sesión cerrada correctamente.');
        $this->redirect('login');
    }
}
