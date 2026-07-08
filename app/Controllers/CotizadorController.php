<?php
namespace App\Controllers;

use App\Models\Producto;
use Dompdf\Dompdf;
use Dompdf\Options;

class CotizadorController extends BaseController {

    public function index() {
        $prodModel = new Producto();
        $productos = $prodModel->getAll();

        $this->view('cotizador/index', [
            'productos' => $productos,
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

        $cotizacion = [
            'cliente' => trim($_POST['cliente'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
            'validez' => trim($_POST['validez'] ?? '7'),
            'fecha' => date('Y-m-d H:i:s'),
            'total' => (float) ($_POST['total_cotizacion'] ?? 0),
        ];

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
