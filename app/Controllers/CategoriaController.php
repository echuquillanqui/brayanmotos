<?php
namespace App\Controllers;

use App\Models\Categoria;

class CategoriaController extends BaseController {

    public function index() {
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->getAll();

        $this->view('categorias/index', [
            'categorias' => $categorias,
            'titulo' => 'Categorías'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? ''
            ];

            if (trim($data['nombre']) === '') {
                header('Location: /categorias?msg=error');
                exit;
            }

            header('Location: /categorias?msg=' . ($categoriaModel->create($data) ? 'guardado' : 'error'));
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $data = [
                'id' => $_POST['id'] ?? null,
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? ''
            ];

            if (!$data['id'] || trim($data['nombre']) === '') {
                header('Location: /categorias?msg=error');
                exit;
            }

            header('Location: /categorias?msg=' . ($categoriaModel->update($data) ? 'actualizado' : 'error'));
        }
    }

    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $ok = $categoriaModel->delete($_POST['id'] ?? null);
            header('Location: /categorias?msg=' . ($ok ? 'eliminado' : 'error'));
        }
    }

    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaModel = new Categoria();
            $ok = $categoriaModel->updateStatus($_POST['id'] ?? null, $_POST['nuevo_estado'] ?? 0);
            header('Location: /categorias?msg=' . ($ok ? 'estado_cambiado' : 'error'));
        }
    }
}
