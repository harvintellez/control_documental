<?php
include 'seguridad.php'; // Protege la página
include 'conexion.php'; // Conecta a la BD

// Consulta para obtener todos los trabajadores usando PDO
$sql = "SELECT * FROM trabajadores ORDER BY fecha_registro DESC";
$resultado = $conexion->query($sql);

include 'includes/header.php';
?>

<div class="row mt-5 mb-5">
    <div class="col-12">

        <?php if (isset($_GET['res'])): ?>
            <?php if ($_GET['res'] == 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Registro guardado con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['res'] == 'del'): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-trash-fill me-2"></i> Registro eliminado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'sin_permiso'): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert" id="alertaSinPermiso">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <strong>Acción no permitida:</strong> Solo los administradores pueden eliminar registros.
                <br><small class="text-muted">Esta página se actualizará automáticamente en <span id="cuentaRegresiva">4</span> segundos...</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                let cuenta = 4;
                const span = document.getElementById('cuentaRegresiva');
                const intervalo = setInterval(() => {
                    cuenta--;
                    if (span) span.textContent = cuenta;
                    if (cuenta <= 0) {
                        clearInterval(intervalo);
                        window.location.href = 'consulta.php';
                    }
                }, 1000);
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> Información actualizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <h5 class="mb-0 fw-bold text-primary">Listado de Trabajadores</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white border-primary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="inputBusqueda" class="form-control border-primary" placeholder="Filtrar por nombre, cédula o código...">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiar">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaTrabajadores">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Perfil</th>
                                <th>Código</th>
                                <th>Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Tipo Oficio</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resultado->rowCount() > 0): ?>
                                <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="ps-3">
                                        <?php if(!empty($row['foto_perfil'])): ?>
                                            <img src="<?php echo htmlspecialchars($row['foto_perfil']); ?>" class="rounded-circle" width="40" height="40">
                                        <?php else: ?>
                                            <i class="bi bi-person-circle text-secondary" style="font-size: 40px;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['codigo_trabajador']); ?></span></td>
                                    <td><div class="fw-bold"><?php echo htmlspecialchars($row['nombre_completo']); ?></div></td>
                                    <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($row['tipo_documento']); ?></small></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <?php if(!empty($row['archivo_adjunto'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['archivo_adjunto']); ?>" target="_blank" class="btn btn-sm btn-info text-white" title="Ver Documento">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm <?php echo ($_SESSION['rol'] === 'admin') ? 'btn-danger' : 'btn-secondary'; ?>" 
                                               <?php if ($_SESSION['rol'] !== 'admin'): ?>
                                               title="Solo los administradores pueden eliminar"
                                               <?php else: ?>
                                               onclick="return confirm('¿Seguro que deseas eliminar este registro?');" 
                                               title="Eliminar"
                                               <?php endif; ?>>
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No se encontraron registros en la base de datos.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small">
                Mostrando resultados de la base de datos local.
            </div>
        </div>
    </div>
</div>

<script>
    // Lógica del Buscador en tiempo real
    const inputBusqueda = document.getElementById('inputBusqueda');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const filas = document.querySelectorAll('#tablaTrabajadores tbody tr');

    inputBusqueda.addEventListener('keyup', function() {
        const filtro = this.value.toLowerCase();
        
        filas.forEach(fila => {
            const contenido = fila.innerText.toLowerCase();
            fila.style.display = contenido.includes(filtro) ? '' : 'none';
        });
    });

    // Lógica del botón Limpiar
    btnLimpiar.addEventListener('click', function() {
        inputBusqueda.value = '';
        filas.forEach(fila => fila.style.display = '');
        inputBusqueda.focus();
    });
</script>

<?php include 'includes/footer.php'; ?>