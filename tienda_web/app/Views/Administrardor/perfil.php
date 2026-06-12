<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-user-shield me-2 text-primary"></i> Mi Perfil de Administrador</h2>
            <p class="text-muted small mb-0">Administra tu información personal y foto de perfil.</p>
        </div>
    </div>

    <?php if(session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4 bg-light">
            
            <form action="<?= base_url('admin/perfil/actualizar') ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    
                    <div class="col-md-4 text-center mb-4 d-flex flex-column align-items-center justify-content-center border-end">
                        <h5 class="fw-bold mb-3">Foto de Perfil</h5>
                        <?php 
                            $foto = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : (session('foto_perfil') ? session('foto_perfil') : 'default.png'); 
                        ?>
                        <img src="<?= base_url('uploads/perfiles/' . $foto) ?>" id="preview_foto" alt="Foto de perfil" class="rounded-circle mb-3 shadow-sm border border-3 border-white" style="width: 150px; height: 150px; object-fit: cover;">                                
                        <label class="btn btn-outline-primary btn-sm rounded-pill px-3 cursor-pointer">
                            <i class="fas fa-camera me-1"></i> Cambiar Foto
                            <input type="file" name="foto_perfil" id="input_foto" accept="image/jpeg, image/png" class="d-none">
                        </label>
                        <small class="text-muted mt-2" style="font-size: 0.75rem;">Formatos aceptados: JPG, PNG.</small>
                    </div>

                    <div class="col-md-8 px-4">
                        <h5 class="fw-bold mb-4">Información de la Cuenta</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" value="<?= esc($usuario['nombre'] ?? session('nombre')) ?>" required>
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
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">
                                    <i class="fas fa-save me-2"></i> Actualizar Mis Datos
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const inputFoto = document.getElementById('input_foto');
    const previewFoto = document.getElementById('preview_foto');

    if(inputFoto) {
        inputFoto.addEventListener('change', function(event) {
            const archivo = event.target.files[0]; 
            if (archivo) {
                const urlTemporal = URL.createObjectURL(archivo);
                previewFoto.src = urlTemporal;
            }
        });
    }
</script>

<?= $this->endSection() ?>