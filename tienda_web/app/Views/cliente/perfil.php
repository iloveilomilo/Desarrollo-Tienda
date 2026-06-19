<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-user-circle me-2 text-primary"></i> Mi Perfil</h2>
            <p class="text-muted small mb-0">Administra tu información personal y tus direcciones de envío.</p>
        </div>
        <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Volver al Panel
        </a>
    </div>

    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <ul class="nav nav-tabs fw-bold" id="perfilTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-muted active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab">
                        <i class="fas fa-id-card me-2"></i>Mis Datos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-muted" id="direcciones-tab" data-bs-toggle="tab" data-bs-target="#direcciones" type="button" role="tab">
                        <i class="fas fa-map-marker-alt me-2"></i>Mis Direcciones
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4 bg-light">
            <div class="tab-content" id="perfilTabsContent">

                <!-- Tab: Datos personales -->
                <div class="tab-pane fade show active" id="datos" role="tabpanel">
                    <form action="<?= base_url('perfil/actualizar_datos') ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 d-flex flex-column align-items-center justify-content-center border-end">
                                <h5 class="fw-bold mb-3">Foto de Perfil</h5>
                                <?php $foto = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'default.png'; ?>
                                <img src="<?= base_url('uploads/perfiles/' . $foto) ?>" id="preview_foto"
                                     alt="Foto de perfil" class="rounded-circle mb-3 shadow-sm border border-3 border-white"
                                     style="width:150px; height:150px; object-fit:cover;">
                                <label class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-camera me-1"></i> Cambiar Foto
                                    <input type="file" name="foto_perfil" id="input_foto" accept="image/jpeg,image/png" class="d-none">
                                </label>
                                <small class="text-muted mt-2" style="font-size:0.75rem;">Formatos aceptados: JPG, PNG.</small>
                            </div>

                            <div class="col-md-8 px-4">
                                <h5 class="fw-bold mb-4">Información de la Cuenta</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nombre(s)</label>
                                        <input type="text" name="nombre" class="form-control" value="<?= esc($usuario['nombre'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Apellidos</label>
                                        <input type="text" name="apellidos" class="form-control" value="<?= esc($usuario['apellidos'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                                        <input type="email" name="correo" class="form-control" value="<?= esc($usuario['correo'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Teléfono Móvil</label>
                                        <input type="text" name="telefono" class="form-control" value="<?= esc($usuario['telefono'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">Actualizar Mis Datos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab: Direcciones -->
                <div class="tab-pane fade" id="direcciones" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Direcciones de Envío Guardadas</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDireccion">
                            <i class="fas fa-plus me-1"></i> Agregar Dirección
                        </button>
                    </div>

                    <div class="row">
                        <?php if (empty($direcciones)): ?>
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-secondary fw-bold">No tienes direcciones guardadas</h6>
                                <p class="text-muted small">Agrega una dirección para poder recibir tus compras.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($direcciones as $dir): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-light shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="fw-bold text-dark mb-1">
                                                    <i class="fas fa-home text-primary me-2"></i><?= esc($dir['calle']) ?> #<?= esc($dir['numero_exterior']) ?>
                                                </h6>
                                                <a href="<?= base_url('perfil/eliminar_direccion/' . $dir['id']) ?>" class="text-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                            <p class="text-muted small mb-1">C.P. <?= esc($dir['codigo_postal']) ?> - Col. <?= esc($dir['colonia']) ?></p>
                                            <p class="text-muted small mb-2"><?= esc($dir['ciudad']) ?>, <?= esc($dir['estado']) ?></p>
                                            <div class="bg-light p-2 rounded small">
                                                <strong>Recibe:</strong> <?= esc($dir['nombre_recibe']) ?> (<?= esc($dir['telefono_recibe']) ?>)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva Dirección -->
<div class="modal fade" id="modalDireccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Nueva Dirección de Envío</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('perfil/guardar_direccion') ?>" method="post">
                <div class="modal-body p-4">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">¿Quién recibe?</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre y Apellido</label>
                            <input type="text" name="nombre_recibe" class="form-control form-control-sm" placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono de contacto</label>
                            <input type="text" name="telefono_recibe" class="form-control form-control-sm" placeholder="Ej. 55 1234 5678" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Datos del domicilio</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Calle</label>
                            <input type="text" name="calle" class="form-control form-control-sm" placeholder="Ej. Av. Reforma" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Nº Exterior</label>
                            <input type="text" name="numero_exterior" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Nº Int. (Opcional)</label>
                            <input type="text" name="numero_interior" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Código Postal</label>
                            <input type="text" name="codigo_postal" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Colonia</label>
                            <input type="text" name="colonia" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ciudad / Municipio</label>
                            <input type="text" name="ciudad" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Estado</label>
                            <input type="text" name="estado" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Referencias (Opcional)</label>
                            <textarea name="referencia" class="form-control form-control-sm" rows="2" placeholder="Ej. Casa blanca con portón negro..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Guardar Dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const inputFoto = document.getElementById('input_foto');
    const previewFoto = document.getElementById('preview_foto');
    if (inputFoto) {
        inputFoto.addEventListener('change', function (e) {
            const archivo = e.target.files[0];
            if (archivo) previewFoto.src = URL.createObjectURL(archivo);
        });
    }
</script>

<?= $this->endSection() ?>
