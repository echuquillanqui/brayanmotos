<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Categorías de Inventario</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary" onclick="abrirModalCrear()">
            <i class="fa-solid fa-plus"></i> Nueva Categoría
        </button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info alert-dismissible fade show">
        <?php
            if($_GET['msg'] == 'guardado') echo "Categoría guardada correctamente.";
            elseif($_GET['msg'] == 'actualizado') echo "Categoría actualizada.";
            elseif($_GET['msg'] == 'estado_cambiado') echo "Estado de la categoría actualizado.";
            elseif($_GET['msg'] == 'eliminado') echo "Categoría eliminada correctamente.";
            else echo "No se pudo completar la operación.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="datatable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categorias as $cat): ?>
                        <tr class="<?php echo ($cat->estado == 0) ? 'table-secondary opacity-75' : ''; ?>">
                            <td><?php echo $cat->id; ?></td>
                            <td><strong><?php echo htmlspecialchars($cat->nombre); ?></strong></td>
                            <td><?php echo htmlspecialchars($cat->descripcion ?? ''); ?></td>
                            <td>
                                <?php if($cat->estado == 1): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick='editarCategoria(<?php echo json_encode($cat); ?>)' title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <?php if($cat->estado == 1): ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cambiarEstado(<?php echo $cat->id; ?>, 0)" title="Desactivar">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-success" onclick="cambiarEstado(<?php echo $cat->id; ?>, 1)" title="Activar">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <form action="/categorias/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría? Los productos asociados quedarán sin categoría.');">
                                        <input type="hidden" name="id" value="<?php echo $cat->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloModal">Nueva Categoría</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCategoria" action="/categorias/guardar" method="POST">
                <input type="hidden" name="id" id="categoriaId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formEstado" action="/categorias/cambiar-estado" method="POST">
    <input type="hidden" name="id" id="idEstado">
    <input type="hidden" name="nuevo_estado" id="nuevoEstado">
</form>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<script>
    const modalCategoria = new bootstrap.Modal(document.getElementById('modalCategoria'));

    function abrirModalCrear() {
        document.getElementById('formCategoria').reset();
        document.getElementById('categoriaId').value = '';
        document.getElementById('tituloModal').innerText = 'Nueva Categoría';
        document.getElementById('formCategoria').action = '/categorias/guardar';
        modalCategoria.show();
    }

    function editarCategoria(cat) {
        document.getElementById('categoriaId').value = cat.id;
        document.getElementById('nombre').value = cat.nombre;
        document.getElementById('descripcion').value = cat.descripcion || '';
        document.getElementById('tituloModal').innerText = 'Editar Categoría';
        document.getElementById('formCategoria').action = '/categorias/actualizar';
        modalCategoria.show();
    }

    function cambiarEstado(id, estado) {
        if(confirm('¿Confirmar cambio de estado?')) {
            document.getElementById('idEstado').value = id;
            document.getElementById('nuevoEstado').value = estado;
            document.getElementById('formEstado').submit();
        }
    }
</script>
