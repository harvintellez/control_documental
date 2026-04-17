<?php 
include 'seguridad.php'; // Solo usuarios logueados pueden registrar
include 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Documento</h4>
            </div>
            <div class="card-body p-4">
                <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código del Trabajador</label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: TR-502" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cédula de Identidad</label>
                            <input type="text" name="cedula" class="form-control" placeholder="001-000000-0000X" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo del Trabajador</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fotografía de Perfil (Opcional)</label>
                        <input type="file" name="foto_perfil" class="form-control" accept="image/jpeg, image/png">
                        <div class="form-text">Formatos permitidos: JPG, PNG.</div>
                    </div>

                    <hr class="my-4">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de Documento</label>
                            <select name="tipo_documento" class="form-select" required>
                                <option value="" selected disabled>Seleccione una opción...</option>
                                <option value="Embargo Judicial">Embargo Judicial</option>
                                <option value="Pensión Alimenticia">Pensión Alimenticia</option>
                                <option value="Otro">Otro Oficio Legal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Documento Original (PDF/Imagen)</label>
                            <input type="file" name="archivo_oficio" class="form-control" accept="application/pdf, image/jpeg, image/png" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valor Inicial</label>
                            <div class="input-group">
                                <span class="input-group-text">VI</span>
                                <input type="number" step="0.01" name="valor_inicial" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valor Final</label>
                            <div class="input-group">
                                <span class="input-group-text">VF</span>
                                <input type="number" step="0.01" name="valor_final" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Breve descripción del oficio</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Ej: Monto total, juzgado emisor, observaciones..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar en Base de Datos
                        </button>
                        <a href="panel.php" class="btn btn-light">Cancelar y Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>