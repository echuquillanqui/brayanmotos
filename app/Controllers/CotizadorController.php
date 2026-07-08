<?php
namespace App\Controllers;

use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Dompdf\Dompdf;
use Dompdf\Options;

class CotizadorController extends BaseController {

    public function index() {
        $prodModel = new Producto();
        $clienteModel = new Cliente();
        $productos = $prodModel->getAll();
        $clientes = $clienteModel->getAll();

        $this->view('cotizador/index', [
            'productos' => $productos,
            'clientes' => $clientes,
            'titulo' => 'Cotizador'
        ]);
    }

    public function imprimir() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cotizador');
            exit;
        }

        $items = json_decode($_POST['productos_json'] ?? '[]', true);
        if (!is_array($items) || count($items) === 0) {
            header('Location: /cotizador?msg=vacio');
            exit;
        }

        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $clienteNombre = trim($_POST['cliente'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if ($clienteId > 0) {
            $clienteModel = new Cliente();
            $clienteRegistrado = $clienteModel->getById($clienteId);
            if ($clienteRegistrado) {
                $clienteNombre = $clienteRegistrado->nombre;
                $telefono = $clienteRegistrado->telefono ?: $telefono;
            }
        }

        $cotizacion = [
            'cliente_id' => $clienteId ?: null,
            'cliente' => $clienteNombre,
            'telefono' => $telefono,
            'observaciones' => trim($_POST['observaciones'] ?? ''),
            'validez' => trim($_POST['validez'] ?? '7'),
            'fecha' => date('Y-m-d H:i:s'),
            'total' => (float) ($_POST['total_cotizacion'] ?? 0),
        ];

        $cotizacionModel = new Cotizacion();
        $cotizacion['id'] = $cotizacionModel->create([
            'cliente_id' => $cotizacion['cliente_id'],
            'cliente_nombre' => $cotizacion['cliente'],
            'telefono' => $cotizacion['telefono'],
            'productos_json' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'total' => $cotizacion['total'],
            'validez' => (int) $cotizacion['validez'],
            'observaciones' => $cotizacion['observaciones'],
            'fecha' => $cotizacion['fecha']
        ]);
        $cotizacion['guardada'] = $cotizacion['id'] > 0;

        $sistema = $this->config;

        ob_start();
        require __DIR__ . '/../Views/cotizador/pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Cotizacion_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
    }
}
