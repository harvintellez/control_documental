<?php
include 'seguridad.php';
include 'conexion.php';

$porPagina = 15;
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($paginaActual - 1) * $porPagina;

$sqlCount = "SELECT COUNT(*) as total FROM trabajadores";
$totalResult = $conexion->query($sqlCount);
$totalRow = $totalResult->fetch(PDO::FETCH_ASSOC);
$totalRegistros = $totalRow['total'];
$totalPaginas = ceil($totalRegistros / $porPagina);

$sql = "SELECT * FROM trabajadores ORDER BY inhabilitado ASC, fecha_registro DESC LIMIT :limit OFFSET :offset";
$resultado = $conexion->prepare($sql);
$resultado->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$resultado->bindValue(':offset', $offset, PDO::PARAM_INT);
$resultado->execute();

include 'includes/header.php';
?>

<div class="row mt-5 mb-5">
    <div class="col-12">

        <?php
        $alertas_inh = [
            'campos_vacios_inh' => '<i class="bi bi-exclamation-circle me-2"></i>Faltan campos obligatorios (fecha o motivo).',
            'fecha_invalida'    => '<i class="bi bi-calendar-x me-2"></i>La fecha de inhabilitación no es válida.',
            'doc_grande_inh'    => '<i class="bi bi-file-earmark-x me-2"></i>El documento supera el límite de 5 MB.',
            'tipo_doc_inh'      => '<i class="bi bi-shield-x me-2"></i>Tipo de archivo no permitido. Solo PDF, JPG o PNG.',
            'sin_permiso'       => '<i class="bi bi-shield-lock-fill me-2"></i><strong>Acción no permitida:</strong> Solo los administradores pueden realizar esta acción.',
            'db'                => '<i class="bi bi-database-x me-2"></i>Error al procesar. Intente de nuevo.',
        ];
        if (isset($_GET['error']) && array_key_exists($_GET['error'], $alertas_inh)):
        ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert" id="alertaError">
                <?php echo $alertas_inh[$_GET['error']]; ?>
                <?php if ($_GET['error'] === 'sin_permiso'): ?>
                    <br><small class="text-muted">Esta página se actualizará en <span id="cuentaRegresiva">4</span> s...</small>
                    <script>
                        let c = 4;
                        const sp = document.getElementById('cuentaRegresiva');
                        const iv = setInterval(() => { c--; if(sp) sp.textContent=c; if(c<=0){clearInterval(iv);location.href='consulta.php';} }, 1000);
                    </script>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['res'])): ?>
            <?php $res = $_GET['res']; ?>
            <?php if ($res === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>Registro guardado con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($res === 'del'): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="bi bi-trash-fill me-2"></i>Registro eliminado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($res === 'inhabilitado'): ?>
                <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                    <i class="bi bi-slash-circle-fill me-2"></i>Embargo inhabilitado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($res === 'reactivado'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="bi bi-check2-circle me-2"></i>Embargo reactivado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($res === 'success'): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i>Información actualizada con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center g-2">
                    <div class="col-md-4">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i>Listado de Embargos</h5>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="inputBusqueda" class="form-control border-primary" placeholder="Filtrar por nombre, cédula o código...">
                            <button class="btn btn-outline-secondary" type="button" id="btnLimpiar"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="form-check form-switch d-inline-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input" type="checkbox" id="mostrarInhabilitados" role="switch">
                            <label class="form-check-label text-muted small fw-semibold" for="mostrarInhabilitados">
                                <i class="bi bi-slash-circle me-1"></i>Mostrar inhabilitados
                            </label>
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
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($resultado->rowCount() > 0): ?>
                            <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php $inh = (bool)$row['inhabilitado']; ?>
                            <tr class="<?php echo $inh ? 'table-secondary' : ''; ?>" data-inhabilitado="<?php echo $inh ? '1' : '0'; ?>">
                                <td class="ps-3">
                                    <?php if (!empty($row['foto_perfil'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['foto_perfil']); ?>"
                                             class="rounded-circle <?php echo $inh ? 'opacity-50' : ''; ?>"
                                             width="40" height="40" style="object-fit:cover;">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-secondary" style="font-size:40px;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['codigo_trabajador']); ?></span></td>
                                <td>
                                    <div class="fw-bold <?php echo $inh ? 'text-decoration-line-through text-muted' : ''; ?>">
                                        <?php echo htmlspecialchars($row['nombre_completo']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($row['tipo_documento']); ?></small></td>
                                <td class="text-center">
                                    <?php if ($inh): ?>
                                        <span class="badge bg-danger"><i class="bi bi-slash-circle me-1"></i>Inhabilitado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group flex-wrap gap-1">
                                        <?php if (!empty($row['archivo_adjunto'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['archivo_adjunto']); ?>"
                                               target="_blank" class="btn btn-sm btn-info text-white" title="Ver Documento Original">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($inh): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-ver-inh"
                                                    title="Ver detalle de inhabilitación"
                                                    data-fecha="<?php echo htmlspecialchars($row['fecha_inhabilitacion'] ?? ''); ?>"
                                                    data-motivo="<?php echo htmlspecialchars($row['motivo_inhabilitacion'] ?? ''); ?>"
                                                    data-doc="<?php echo htmlspecialchars($row['doc_inhabilitacion'] ?? ''); ?>"
                                                    data-usuario="<?php echo htmlspecialchars($row['usuario_inhabilito'] ?? ''); ?>"
                                                    data-bs-toggle="modal" data-bs-target="#modalVerInh">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                            <?php if ($_SESSION['rol'] === 'admin'): ?>
                                                <a href="reactivar.php?id=<?php echo $row['id']; ?>"
                                                   class="btn btn-sm btn-success" title="Reactivar embargo"
                                                   onclick="return confirm('¿Reactivar este embargo?');">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="editar.php?id=<?php echo $row['id']; ?>"
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if ($_SESSION['rol'] === 'admin'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-inh"
                                                        title="Inhabilitar embargo"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($row['nombre_completo']); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#modalInhabilitar">
                                                    <i class="bi bi-slash-circle"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary" disabled
                                                        title="Solo administradores pueden inhabilitar">
                                                    <i class="bi bi-slash-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <a href="eliminar.php?id=<?php echo $row['id']; ?>"
                                           class="btn btn-sm <?php echo ($_SESSION['rol'] === 'admin') ? 'btn-danger' : 'btn-secondary'; ?>"
                                           <?php if ($_SESSION['rol'] === 'admin'): ?>
                                               onclick="return confirm('¿Seguro que deseas eliminar este registro?');" title="Eliminar"
                                           <?php else: ?>
                                               title="Solo los administradores pueden eliminar"
                                           <?php endif; ?>>
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No se encontraron registros en la base de datos.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <div class="text-muted small">
                        Base de datos local — <span id="contadorRegistros"></span>
                    </div>
                    <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Paginación">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?php echo $paginaActual <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>">«</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPaginas || ($i >= $paginaActual - 1 && $i <= $paginaActual + 1)): ?>
                                    <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif ($i == $paginaActual - 2 || $i == $paginaActual + 2): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $paginaActual >= $totalPaginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>">»</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: Inhabilitar ===== -->
<div class="modal fade" id="modalInhabilitar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="inhabilitar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="inhId">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-slash-circle-fill me-2"></i>Inhabilitar Embargo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Inhabilitando embargo de: <strong id="inhNombre"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Inhabilitación <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inhabilitacion" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo / Comentarios <span class="text-danger">*</span></label>
                        <textarea name="motivo_inhabilitacion" class="form-control" rows="4" required
                                  placeholder="Describa el motivo legal que sustenta la inhabilitación..."></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold">Documento Legal de Soporte <small class="text-muted">(PDF, JPG, PNG — máx. 5 MB)</small></label>
                        <input type="file" name="doc_inhabilitacion" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Adjunte el oficio o resolución que sustenta la inhabilitación.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="bi bi-slash-circle-fill me-1"></i>Confirmar Inhabilitación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL: Ver Inhabilitación ===== -->
<div class="modal fade" id="modalVerInh" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle-fill me-2"></i>Detalle de Inhabilitación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Fecha:</dt>       <dd class="col-sm-8" id="detFecha">—</dd>
                    <dt class="col-sm-4">Inhabilitado por:</dt> <dd class="col-sm-8" id="detUsuario">—</dd>
                    <dt class="col-sm-4">Motivo:</dt>      <dd class="col-sm-8" id="detMotivo" style="white-space:pre-wrap;">—</dd>
                    <dt class="col-sm-4">Documento:</dt>   <dd class="col-sm-8" id="detDoc">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
const inputBusqueda = document.getElementById('inputBusqueda');
const btnLimpiar    = document.getElementById('btnLimpiar');
const switchInh     = document.getElementById('mostrarInhabilitados');
const allFilas      = document.querySelectorAll('#tablaTrabajadores tbody tr[data-inhabilitado]');
const contador      = document.getElementById('contadorRegistros');

function aplicarFiltros() {
    const filtro = inputBusqueda.value.toLowerCase();
    const verInh = switchInh.checked;
    let visibles = 0;
    allFilas.forEach(fila => {
        const esInh    = fila.dataset.inhabilitado === '1';
        const coincide = fila.innerText.toLowerCase().includes(filtro);
        const mostrar  = coincide && (verInh || !esInh);
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
    const total = <?php echo $totalRegistros; ?>;
    if (contador) contador.textContent = visibles + ' de ' + total + ' registro' + (total !== 1 ? 's' : '') + ' visible' + (total !== 1 ? 's' : '');
}

inputBusqueda.addEventListener('keyup', aplicarFiltros);
switchInh.addEventListener('change', aplicarFiltros);
btnLimpiar.addEventListener('click', () => { inputBusqueda.value = ''; aplicarFiltros(); inputBusqueda.focus(); });
aplicarFiltros(); // Ocultar inhabilitados al cargar

// Modal inhabilitar
document.querySelectorAll('.btn-inh').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('inhId').value = btn.dataset.id;
        document.getElementById('inhNombre').textContent = btn.dataset.nombre;
    });
});

// Modal ver inhabilitación
document.querySelectorAll('.btn-ver-inh').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('detFecha').textContent   = btn.dataset.fecha   || '—';
        document.getElementById('detUsuario').textContent = btn.dataset.usuario || '—';
        document.getElementById('detMotivo').textContent  = btn.dataset.motivo  || '—';
        const doc = btn.dataset.doc;
        document.getElementById('detDoc').innerHTML = doc
            ? `<a href="${doc}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf me-1"></i>Ver documento</a>`
            : 'Sin documento adjunto';
    });
});
</script>

<?php include 'includes/footer.php'; ?>