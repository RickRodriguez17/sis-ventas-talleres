<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index(): void
    {
        Auth::requireRoles(['administrador', 'cajero']);
        $this->render('products/index', ['title' => 'Productos', 'products' => (new Product())->all(), 'canManage' => Auth::isAdmin()]);
    }

    public function create(): void
    {
        Auth::requireRoles(['administrador']);
        $this->renderForm('Nuevo producto', null);
    }

    public function store(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $data = $this->payload();
        $data['image'] = $this->saveUpload('image', 'products');
        $id = (new Product())->create($data, $_POST['combo_items'] ?? []);
        (new AuditLog())->register(Auth::id(), 'crear', 'products', $id, $data['name']);
        Flash::set('success', 'Producto creado correctamente.');
        $this->redirect('productos');
    }

    public function edit(): void
    {
        Auth::requireRoles(['administrador']);
        $product = (new Product())->find((int) ($_GET['id'] ?? 0));

        if ($product === null) {
            Flash::set('error', 'Producto no encontrado.');
            $this->redirect('productos');
        }

        $this->renderForm('Editar producto', $product);
    }

    public function update(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        $id = (int) ($_GET['id'] ?? 0);
        $productModel = new Product();
        $product = $productModel->find($id);

        if ($product === null) {
            Flash::set('error', 'Producto no encontrado.');
            $this->redirect('productos');
        }

        $data = $this->payload();
        $data['image'] = $this->saveUpload('image', 'products', $product['image'] ?? null);
        $productModel->update($id, $data, $_POST['combo_items'] ?? []);
        (new AuditLog())->register(Auth::id(), 'actualizar', 'products', $id, $data['name']);
        Flash::set('success', 'Producto actualizado.');
        $this->redirect('productos');
    }

    public function delete(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();

        if ((new Product())->delete((int) ($_POST['id'] ?? 0))) {
            Flash::set('success', 'Producto eliminado.');
        } else {
            Flash::set('error', 'No se puede eliminar un producto vendido.');
        }

        $this->redirect('productos');
    }

    public function toggle(): void
    {
        Auth::requireRoles(['administrador']);
        $this->validateToken();
        (new Product())->toggle((int) ($_POST['id'] ?? 0));
        Flash::set('success', 'Estado del producto actualizado.');
        $this->redirect('productos');
    }

    private function renderForm(string $title, ?array $product): void
    {
        $productModel = new Product();
        $this->render('products/form', [
            'title' => $title,
            'product' => $product,
            'categories' => (new Category())->all(true),
            'products' => $productModel->all(true),
            'comboItems' => $product === null ? [] : $productModel->comboItems((int) $product['id']),
        ]);
    }

    private function payload(): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);

        if ($name === '' || $price <= 0 || (int) ($_POST['category_id'] ?? 0) <= 0) {
            Flash::set('error', 'Completa nombre, categoría y precio válido.');
            redirect_back('productos');
        }

        return [
            'category_id' => (int) $_POST['category_id'],
            'name' => $name,
            'slug' => slugify($name),
            'price' => $price,
            'image' => null,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'is_combo' => isset($_POST['is_combo']) ? 1 : 0,
            'status' => (string) ($_POST['status'] ?? 'activo') === 'activo' ? 'activo' : 'inactivo',
        ];
    }

    private function validateToken(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            Flash::set('error', 'La sesión expiró.');
            $this->redirect('productos');
        }
    }
}
