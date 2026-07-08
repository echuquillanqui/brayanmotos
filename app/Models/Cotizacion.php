<?php
namespace App\Models;

use PDO;

class Cotizacion extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable() {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS cotizaciones (
                id INT NOT NULL AUTO_INCREMENT,
                cliente_id INT DEFAULT NULL,
                cliente_nombre VARCHAR(150) NOT NULL,
                telefono VARCHAR(20) DEFAULT NULL,
                productos_json LONGTEXT NOT NULL,
                total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                validez INT NOT NULL DEFAULT 7,
                observaciones TEXT DEFAULT NULL,
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY cliente_id (cliente_id),
                CONSTRAINT cotizaciones_ibfk_1 FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // La app puede seguir imprimiendo aunque la copia no se pueda guardar.
        }
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO cotizaciones (cliente_id, cliente_nombre, telefono, productos_json, total, validez, observaciones, fecha)
                    VALUES (:cliente_id, :cliente_nombre, :telefono, :productos_json, :total, :validez, :observaciones, :fecha)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':cliente_id' => $data['cliente_id'] ?: null,
                ':cliente_nombre' => $data['cliente_nombre'],
                ':telefono' => $data['telefono'],
                ':productos_json' => $data['productos_json'],
                ':total' => $data['total'],
                ':validez' => $data['validez'],
                ':observaciones' => $data['observaciones'],
                ':fecha' => $data['fecha']
            ]) ? (int) $this->db->lastInsertId() : 0;
        } catch (\Exception $e) { return 0; }
    }

    public function getByCliente($clienteId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cotizaciones WHERE cliente_id = :id ORDER BY fecha DESC");
            $stmt->execute([':id' => $clienteId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\Exception $e) { return []; }
    }
}
