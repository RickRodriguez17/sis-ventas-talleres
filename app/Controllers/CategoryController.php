<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Category;

final class CategoryController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('categories/index', ['title' => 'Categorías', 'categories' => (new Category())->all()]);
    }

    public function create(): void
    {
        Auth::requireRoles(['administrador']);
        $this->render('categories/form', ['title' => 'Nueva categoría', 'category' => null]);
    }

    public function store(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $name = trim((string) ($_POST['name'] ?? ''));

        if ($name === '') {
            Flash::set('error', 'El nombre es obligatorio.');
            $this->redirect('categorias/crear');
        }

        $id = (new Category())->create([
            'name' => $name,
            'slug' => slugify($name),
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);
        (new AuditLog())->register(Auth::id(), 'crear', 'categories', $id, $name);
        Flash::set('success', 'Categoría creada correctamente.');
        $this->redirect('categorias');
    }

    public function edit(): void
    {
        Auth::requireRoles(['administrador']);
        $category = (new Category())->find((int) ($_GET['id'] ?? 0));

        if ($category === null) {
            Flash::set('error', 'Categoría no encontrada.');
            $this->redirect('categorias');
        }

        $this->render('categories/form', ['title' => 'Editar categoría', 'category' => $category]);
    }

    public function update(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $id = (int) ($_GET['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));

        if ($id <= 0 || $name === '') {
            Flash::set('error', 'Datos incompletos.');
            $this->redirect('categorias');
        }

        (new Category())->update($id, [
            'name' => $name,
            'slug' => slugify($name),
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'status' => isset($_POST['status']) ? 1 : 0,
        ]);
        (new AuditLog())->register(Auth::id(), 'actualizar', 'categories', $id, $name);
        Flash::set('success', 'Categoría actualizada.');
        $this->redirect('categorias');
    }

    public function delete(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();

        if ((new Category())->delete((int) ($_POST['id'] ?? 0))) {
            Flash::set('success', 'Categoría eliminada.');
        } else {
            Flash::set('error', 'No se puede eliminar una categoría con productos.');
        }

        $this->redirect('categorias');
    }

    public function toggle(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        (new Category())->toggle((int) ($_POST['id'] ?? 0));
        Flash::set('success', 'Estado actualizado.');
        $this->redirect('categorias');
    }

    private function validateToken(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('categorias');
        }
    }
}
