<?php
include 'seguridad.php';
include 'conexion.php';

$resultados = [];
$mensaje = '';

// Procesamiento de búsqueda y exportación a Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigos_raw = $_POST['codigos'];
    $codigos = array_filter(array_map('trim', explode(',', $codigos_raw)), function($c) { return $c !== ''; });
    if (count($codigos) > 0) {
        // Crear placeholders para PDO (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($codigos), '?'));
        
        $sql = "SELECT id, codigo_trabajador, nombre_completo, cedula, foto_perfil, descripcion_oficio, archivo_adjunto, tipo_documento, fecha_registro, valor_inicial, valor_final, inhabilitado FROM trabajadores WHERE codigo_trabajador IN ($placeholders)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array_values($codigos));
        
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        // Exportar a Excel
        if (isset($_POST['exportar']) && count($resultados) > 0) {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=busqueda_trabajadores_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr style="background-color: #0d6efd; color: white;">';
            echo '<th colspan="12" style="font-size: 16px;">SISTEMA DE CONTROL DOCUMENTAL - BÚSQUEDA DE TRABAJADORES</th>';
            echo '</tr>';
            echo '<tr style="background-color: #333; color: white;">';
            echo '<th>Código Trabajador</th>';
            echo '<th>Nombre Completo</th>';
            echo '<th>Cédula</th>';
            echo '<th>Tipo Documento</th>';
            echo '<th>Descripción Oficio</th>';
            echo '<th>Documento Adjunto</th>';
            echo '<th>Fecha Registro</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($resultados as $trabajador) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($trabajador['codigo_trabajador']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['nombre_completo']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['cedula']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['tipo_documento'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['descripcion_oficio'] ?? '') . '</td>';
                $doc_nombre = !empty($trabajador['archivo_adjunto']) ? basename($trabajador['archivo_adjunto']) : 'Sin documento';
                echo '<td>' . htmlspecialchars($doc_nombre) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['fecha_registro'] ?? '') . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            exit;
        }
    } else {
        $mensaje = "Debe ingresar al menos un código.";
    }
}


include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary"><i class="bi bi-search"></i> Buscar varios trabajadores</h2>
        <p class="text-muted">Ingrese uno o más <b>códigos de trabajador</b> separados por comas para obtener los datos correspondientes.</p>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="codigos" class="form-label">Códigos de trabajador (separados por coma)</label>
                        <textarea name="codigos" id="codigos" class="form-control" rows="3" placeholder="Ej: 123, 456, 789"><?php if (isset($_POST['codigos'])) echo htmlspecialchars($_POST['codigos']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <?php if (!empty($resultados)): ?>
                    <button type="submit" name="exportar" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($resultados)): ?>
<div class="row mb-5">
    <div class="col">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Resultados encontrados</h5>
                <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i>Haga <b>doble clic</b> en una fila para ver el detalle completo del trabajador.</p>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Código Trabajador</th>
                                <th>Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Descripción Oficio</th>
                                <th>Documento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $trabajador): ?>
                                <tr ondblclick="abrirModalTrabajador(this)"
                                    data-id="<?php echo $trabajador['id']; ?>"
                                    data-codigo="<?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?>"
                                    data-nombre="<?php echo htmlspecialchars($trabajador['nombre_completo']); ?>"
                                    data-cedula="<?php echo htmlspecialchars($trabajador['cedula']); ?>"
                                    data-foto="<?php echo htmlspecialchars($trabajador['foto_perfil'] ?? ''); ?>"
                                    data-descripcion="<?php echo htmlspecialchars($trabajador['descripcion_oficio'] ?? ''); ?>"
                                    data-archivo="<?php echo htmlspecialchars($trabajador['archivo_adjunto'] ?? ''); ?>"
                                    data-tipo="<?php echo htmlspecialchars($trabajador['tipo_documento'] ?? ''); ?>"
                                    data-fecha-registro="<?php echo $trabajador['fecha_registro'] ?? ''; ?>"
                                    data-valor-inicial="<?php echo $trabajador['valor_inicial'] ?? ''; ?>"
                                    data-valor-final="<?php echo $trabajador['valor_final'] ?? ''; ?>"
                                    data-inhabilitado="<?php echo isset($trabajador['inhabilitado']) && $trabajador['inhabilitado'] ? '1' : '0'; ?>"
                                    style="cursor: pointer;">
                                    <td><?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['cedula']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['descripcion_oficio']); ?></td>
                                    <td>
                                        <?php if (!empty($trabajador['archivo_adjunto'])): ?>
                                            <a href="<?php echo htmlspecialchars($trabajador['archivo_adjunto']); ?>"
                                               target="_blank" class="btn btn-sm btn-info text-white"
                                               title="Ver documento: <?php echo htmlspecialchars(basename($trabajador['archivo_adjunto'])); ?>"
                                               onclick="event.stopPropagation();">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($trabajador['inhabilitado']) && $trabajador['inhabilitado']): ?>
                                            <span class="badge bg-danger"><i class="bi bi-slash-circle me-1"></i>Inhabilitado</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="buscar_trabajadores.php" class="btn btn-outline-dark mt-3"><i class="bi bi-arrow-left"></i> Nueva búsqueda</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== MODAL: Ver Trabajador (solo lectura) ===== -->
<div class="modal fade" id="modalVerTrabajador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Detalle del Trabajador
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3 text-center">
                        <div id="fotoModal" class="rounded-circle mx-auto mb-2 bg-light p-2 d-flex align-items-center justify-content-center overflow-hidden" style="width:100px;height:100px;">
                            <i class="bi bi-person-circle text-secondary" style="font-size:3.5rem;"></i>
                        </div>
                        <div class="small" id="estadoModal">Cargando...</div>
                    </div>
                    <div class="col-md-9">
                        <h5 class="fw-bold mb-1" id="nombreModal">—</h5>
                        <span class="badge bg-secondary fs-6 mb-2" id="codigoModal">—</span>
                        <div class="mb-2">
                            <small class="text-muted">Cédula:</small>
                            <span class="fw-semibold ms-1" id="cedulaModal">—</span>
                        </div>
                        <div id="documentoModal" class="mb-2"></div>
                    </div>
                </div>
                <hr>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Tipo Documento:</dt>
                    <dd class="col-sm-8" id="tipoModal">—</dd>
                    
                    <dt class="col-sm-4">Fecha Registro:</dt>
                    <dd class="col-sm-8" id="fechaRegistroModal">—</dd>
                    
                    <dt class="col-sm-4">Usuario Registro:</dt>
                    <dd class="col-sm-8" id="usuarioRegistroModal">—</dd>
                    
                    <dt class="col-sm-4">Nombre Adjunto:</dt>
                    <dd class="col-sm-8" id="nombreAdjuntoModal">—</dd>
                    
                    <dt class="col-sm-4">Valor Inicial:</dt>
                    <dd class="col-sm-8" id="valorInicialModal">—</dd>
                    
                    <dt class="col-sm-4">Valor Final:</dt>
                    <dd class="col-sm-8" id="valorFinalModal">—</dd>
                </dl>
                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-2"><i class="bi bi-card-text me-2"></i>Descripción del Oficio</h6>
                    <div id="descripcionModal" class="small" style="white-space:pre-wrap;max-height:200px;overflow-y:auto;">—</div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="btnVerDocumentoModal" href="#" target="_blank" class="btn btn-info text-white me-auto" style="display:none;">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Ver Documento
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalTrabajador(fila) {
    const modalEl = document.getElementById('modalVerTrabajador');
    const modal = new bootstrap.Modal(modalEl);
    
    // Poblar campos desde data-* attributes
    document.getElementById('codigoModal').textContent = fila.dataset.codigo;
    document.getElementById('nombreModal').textContent = fila.dataset.nombre;
    document.getElementById('cedulaModal').textContent = fila.dataset.cedula;
    document.getElementById('tipoModal').textContent = fila.dataset.tipo || '—';
    document.getElementById('fechaRegistroModal').textContent = fila.dataset.fechaRegistro ? new Date(fila.dataset.fechaRegistro).toLocaleString('es-NI') : '—';
    document.getElementById('usuarioRegistroModal').textContent = fila.dataset.usuarioRegistro || '—';
    document.getElementById('nombreAdjuntoModal').textContent = fila.dataset.nombreAdjunto || '—';
    document.getElementById('valorInicialModal').textContent = fila.dataset.valorInicial && !isNaN(fila.dataset.valorInicial) ? 'C$ ' + parseFloat(fila.dataset.valorInicial).toFixed(2) : '—';
    document.getElementById('valorFinalModal').textContent = fila.dataset.valorFinal && !isNaN(fila.dataset.valorFinal) ? 'C$ ' + parseFloat(fila.dataset.valorFinal).toFixed(2) : '—';
    document.getElementById('descripcionModal').textContent = fila.dataset.descripcion || 'Sin descripción';
    
    // Foto de perfil
    const foto = fila.dataset.foto;
    const fotoEl = document.getElementById('fotoModal');
    if (foto) {
        fotoEl.innerHTML = `<img src="${foto}" class="rounded-circle w-100 h-100" style="object-fit:cover;">`;
    } else {
        fotoEl.innerHTML = '<i class="bi bi-person-circle text-secondary" style="font-size:3.5rem;"></i>';
    }
    
    // Estado
    const inh = fila.dataset.inhabilitado === '1';
    document.getElementById('estadoModal').innerHTML = inh 
        ? '<span class="badge bg-danger"><i class="bi bi-slash-circle me-1"></i>Inhabilitado</span>'
        : '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>';
    
    // Documento adjunto
    const archivo = fila.dataset.archivo;
    const docEl = document.getElementById('documentoModal');
    const btnDocModal = document.getElementById('btnVerDocumentoModal');
    
    if (archivo) {
        docEl.innerHTML = `
            <a href="${archivo}" target="_blank" class="btn btn-sm btn-info text-white">
                <i class="bi bi-file-earmark-pdf me-1"></i>Ver Documento Adjunto
            </a>
            <span class="text-muted small ms-2">${archivo.split('/').pop()}</span>
        `;
        btnDocModal.href = archivo;
        btnDocModal.style.display = 'inline-block';
    } else {
        docEl.innerHTML = '<span class="text-muted small"><i class="bi bi-file-earmark-x me-1"></i>Sin documento adjunto</span>';
        btnDocModal.style.display = 'none';
    }
    
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
