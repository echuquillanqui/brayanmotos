<?php
namespace App\Models;

use PDO;

class Categoria extends BaseModel {

    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getActivas() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias WHERE estado = 1 ORDER BY nombre ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO categorias (nombre, descripcion, estado) VALUES (:nombre, :descripcion, 1)");
            return $stmt->execute([
                ':nombre' => trim($data['nombre']),
                ':descripcion' => trim($data['descripcion'] ?? '')
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($data) {
        try {
            $stmt = $this->db->prepare("UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id");
            return $stmt->execute([
                ':nombre' => trim($data['nombre']),
                ':descripcion' => trim($data['descripcion'] ?? ''),
                ':id' => $data['id']
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateStatus($id, $estado) {
        try {
            $stmt = $this->db->prepare("UPDATE categorias SET estado = :estado WHERE id = :id");
            return $stmt->execute([':estado' => $estado, ':id' => $id]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
