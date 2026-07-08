<?php
$logoBase64 = null;
if (!empty($sistema->logo)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/logo/' . $sistema->logo;
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
$cliente = $cotizacion['cliente'] ?: 'Público general';
$fecha = date('d/m/Y H:i', strtotime($cotizacion['fecha']));
$vence = date('d/m/Y', strtotime($cotizacion['fecha'] . ' +' . (int) $cotizacion['validez'] . ' days'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
        .header { display: table; width: 100%; border-bottom: 2px solid #0d6efd; padding-bottom: 14px; margin-bottom: 18px; }
        .brand, .doc-info { display: table-cell; vertical-align: top; }
        .doc-info { text-align: right; }
        .logo { max-height: 65px; margin-bottom: 6px; }
        h1 { margin: 0; color: #0d6efd; font-size: 24px; }
        h2 { margin: 0 0 6px; font-size: 17px; }
        .muted { color: #666; }
        .box { border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; text-align: left; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; font-size: 14px; background: #f8f9fa; }
        .footer { margin-top: 24px; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <?php if($logoBase64): ?><img src="<?php echo $logoBase64; ?>" class="logo"><br><?php endif; ?>
            <h2><?php echo htmlspecialchars($sistema->nombre_sistema, ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="muted">
                <?php echo htmlspecialchars($sistema->direccion ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                Tel: <?php echo htmlspecialchars($sistema->telefono ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div class="doc-info">
            <h1>COTIZACIÓN</h1>
            <div>Fecha: <?php echo $fecha; ?></div>
            <div>Válida hasta: <?php echo $vence; ?></div>
        </div>
    </div>

    <div class="box">
        <strong>Cliente:</strong> <?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?><br>
        <?php if(!empty($cotizacion['telefono'])): ?>
            <strong>Teléfono:</strong> <?php echo htmlspecialchars($cotizacion['telefono'], ENT_QUOTES, 'UTF-8'); ?><br>
        <?php endif; ?>
        <strong>Validez:</strong> <?php echo (int) $cotizacion['validez']; ?> día(s)
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Código</th>
                <th>Producto</th>
                <th width="12%" class="text-center">Cant.</th>
                <th width="16%" class="text-right">Precio</th>
                <th width="16%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['codigo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($item['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-center"><?php echo (int) ($item['cantidad'] ?? 0); ?></td>
                    <td class="text-right"><?php echo $sistema->simbolo_moneda . ' ' . number_format((float) ($item['precio'] ?? 0), 2); ?></td>
                    <td class="text-right"><?php echo $sistema->simbolo_moneda . ' ' . number_format((float) ($item['subtotal'] ?? 0), 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right"><?php echo $sistema->simbolo_moneda . ' ' . number_format((float) $cotizacion['total'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <?php if(!empty($cotizacion['observaciones'])): ?>
        <div class="box" style="margin-top:15px;">
            <strong>Observaciones:</strong><br>
            <?php echo nl2br(htmlspecialchars($cotizacion['observaciones'], ENT_QUOTES, 'UTF-8')); ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        Esta cotización es informativa y no descuenta inventario. Los precios y disponibilidad pueden variar al confirmar la venta.
    </div>
</body>
</html>
