<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h2 mb-1"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Cotizador</h1>
        <p class="text-muted mb-0">Busca productos del inventario, arma la cotización, vincula clientes habituales y guarda una copia para verificación.</p>
    </div>
</div>

<?php if(isset($_GET['msg']) && $_GET['msg'] === 'vacio'): ?>
    <div class="alert alert-warning">Agrega al menos un producto antes de imprimir la cotización.</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-bold">Buscar producto del inventario</label>
                <div class="d-flex gap-2">
                    <select id="selectProducto" class="form-select select2">
                        <option value="">Escriba código o nombre para buscar...</option>
                        <?php foreach($productos as $p): ?>
                            <?php if($p->estado == 1): ?>
                                <option value="<?php echo (int) $p->id; ?>"
                                        data-codigo="<?php echo htmlspecialchars($p->codigo ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-nombre="<?php echo htmlspecialchars($p->nombre ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-precio="<?php echo (float) $p->precio_venta; ?>"
                                        data-stock="<?php echo (int) $p->stock; ?>">
                                    <?php echo htmlspecialchars(($p->codigo ? $p->codigo . ' - ' : '') . $p->nombre, ENT_QUOTES, 'UTF-8'); ?>
                                    (<?php echo $sistema->simbolo_moneda . ' ' . number_format($p->precio_venta, 2); ?>) | Stock: <?php echo (int) $p->stock; ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary" onclick="agregarProducto()">
                        <i class="fa-solid fa-plus"></i> Agregar
                    </button>
                </div>
                <div class="form-text">La cotización no descuenta stock; solo toma precios y referencias actuales del inventario.</div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="fa-solid fa-list-check me-2"></i> Productos cotizados
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center" width="130">Precio</th>
                            <th class="text-center" width="110">Cant.</th>
                            <th class="text-end" width="130">Subtotal</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductos">
                        <tr id="filaVacia">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-circle-plus fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Aún no hay productos en la cotización.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-info text-white mb-3">
            <div class="card-body text-center">
                <h6 class="text-white-50 text-uppercase">Total cotizado</h6>
                <h1 class="display-5 fw-bold" id="displayTotal"><?php echo $sistema->simbolo_moneda; ?> 0.00</h1>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Datos para el PDF</div>
            <div class="card-body">
                <form action="/cotizador/imprimir" method="POST" id="formCotizacion" target="_blank">
                    <input type="hidden" name="productos_json" id="inputProductos">
                    <input type="hidden" name="total_cotizacion" id="inputTotal" value="0">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente habitual registrado</label>
                        <select name="cliente_id" id="selectCliente" class="form-select select2" onchange="seleccionarCliente()">
                            <option value="">Buscar cliente registrado...</option>
                            <?php foreach(($clientes ?? []) as $cliente): ?>
                                <?php if((int) $cliente->estado === 1): ?>
                                    <option value="<?php echo (int) $cliente->id; ?>"
                                            data-nombre="<?php echo htmlspecialchars($cliente->nombre ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            data-telefono="<?php echo htmlspecialchars($cliente->telefono ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($cliente->nombre, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if(!empty($cliente->telefono)): ?> | <?php echo htmlspecialchars($cliente->telefono, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Si es cliente habitual, selecciónalo para que la cotización quede guardada en su perfil.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente / Empresa</label>
                        <input type="text" name="cliente" id="inputCliente" class="form-control" placeholder="Público general">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" id="inputTelefono" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Validez (días)</label>
                        <input type="number" name="validez" class="form-control" value="7" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Opcional"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-danger btn-lg" onclick="imprimirCotizacion()">
                            <i class="fa-solid fa-file-pdf me-2"></i> Imprimir PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
    let cotizacion = [];
    const simboloMoneda = <?php echo json_encode($sistema->simbolo_moneda); ?>;

    function seleccionarCliente() {
        const option = $('#selectCliente').find(':selected');
        if (!option.val()) return;
        document.getElementById('inputCliente').value = option.data('nombre') || '';
        document.getElementById('inputTelefono').value = option.data('telefono') || '';
    }

    function agregarProducto() {
        const select = $('#selectProducto');
        const id = select.val();
        if (!id) return alert('Seleccione un producto.');

        const option = select.find(':selected');
        const existe = cotizacion.find(p => p.id == id);

        if (existe) {
            existe.cantidad++;
            existe.subtotal = existe.cantidad * existe.precio;
        } else {
            cotizacion.push({
                id: id,
                codigo: option.data('codigo') || '',
                nombre: option.data('nombre'),
                precio: parseFloat(option.data('precio')) || 0,
                stock: parseInt(option.data('stock'), 10) || 0,
                cantidad: 1,
                subtotal: parseFloat(option.data('precio')) || 0
            });
        }

        select.val(null).trigger('change');
        renderizarTabla();
    }

    function eliminarProducto(index) {
        cotizacion.splice(index, 1);
        renderizarTabla();
    }

    function actualizarCantidad(index, cantidad) {
        const producto = cotizacion[index];
        let nuevaCantidad = parseInt(cantidad, 10);
        if (isNaN(nuevaCantidad) || nuevaCantidad < 1) nuevaCantidad = 1;

        producto.cantidad = nuevaCantidad;
        producto.subtotal = producto.cantidad * producto.precio;
        renderizarTabla();
    }

    function escaparHtml(texto) {
        return String(texto ?? '').replace(/[&<>'"]/g, function(caracter) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[caracter];
        });
    }

    function renderizarTabla() {
        const tbody = document.getElementById('tablaProductos');
        const filaVacia = document.getElementById('filaVacia');
        tbody.innerHTML = '';

        if (cotizacion.length === 0) {
            tbody.appendChild(filaVacia);
            document.getElementById('displayTotal').innerText = simboloMoneda + ' 0.00';
            document.getElementById('inputTotal').value = 0;
            document.getElementById('inputProductos').value = '';
            return;
        }

        let total = 0;
        cotizacion.forEach((prod, index) => {
            total += prod.subtotal;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-bold">${escaparHtml(prod.nombre)}</div>
                    <small class="text-muted">${prod.codigo ? 'Código: ' + escaparHtml(prod.codigo) + ' | ' : ''}Stock actual: ${prod.stock}</small>
                </td>
                <td class="text-center">${simboloMoneda} ${prod.precio.toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center mx-auto" style="max-width: 90px;" min="1" value="${prod.cantidad}" onchange="actualizarCantidad(${index}, this.value)">
                </td>
                <td class="text-end fw-bold">${simboloMoneda} ${prod.subtotal.toFixed(2)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm text-danger" onclick="eliminarProducto(${index})"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('displayTotal').innerText = simboloMoneda + ' ' + total.toFixed(2);
        document.getElementById('inputTotal').value = total.toFixed(2);
        document.getElementById('inputProductos').value = JSON.stringify(cotizacion);
    }

    function imprimirCotizacion() {
        if (cotizacion.length === 0) return alert('Agrega al menos un producto a la cotización.');
        document.getElementById('formCotizacion').submit();
    }
</script>
