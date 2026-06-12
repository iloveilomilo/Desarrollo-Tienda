<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Categorías</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
        <i class="fas fa-plus me-2"></i>Nueva Categoría
    </button>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-info"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $index => $cat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $cat['nombre'] ?></td>
                            <td>
                                <!-- Muestra el badge dependiendo del estado -->
                                <?php if ($cat['activo'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="fas fa-ban me-1"></i>Desactivada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <!-- Botones Dinámicos -->
                                <?php if ($cat['activo'] == 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDesactivarCategoria"
                                        data-url="<?= base_url('admin/categorias/eliminar/' . $cat['id']) ?>">
                                        <i class="fas fa-trash-alt me-1"></i> Desactivar
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalReactivarCategoria"
                                        data-url="<?= base_url('admin/categorias/reactivar/' . $cat['id']) ?>">
                                        <i class="fas fa-check-circle me-1"></i> Reactivar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No hay categorías registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/categorias/guardar') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" name="nombre" required placeholder="Ej: Celulares, Tablets...">
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

<!-- Modal Desactivar Categoría (Rojo) -->
<div class="modal fade" id="modalDesactivarCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-circle me-2"></i> Confirmar Desactivación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-eye-slash fa-3x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">¿Ocultar esta categoría?</h5>
                <p class="text-muted mb-0">La categoría dejará de ser visible en la tienda pública, pero se conservará en el sistema.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnEjecutarDesactivarCat" class="btn btn-danger px-4 rounded-pill fw-bold">Sí, desactivar</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reactivar Categoría (Verde) -->
<div class="modal fade" id="modalReactivarCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i> Confirmar Reactivación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-eye fa-3x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">¿Reactivar esta categoría?</h5>
                <p class="text-muted mb-0">La categoría volverá a estar disponible para clasificar productos y se mostrará en la tienda pública.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnEjecutarReactivarCat" class="btn btn-success px-4 rounded-pill fw-bold">Sí, reactivar</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal Desactivar
        var modalDesactivar = document.getElementById('modalDesactivarCategoria');
        if (modalDesactivar) {
            modalDesactivar.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var btnConfirmar = modalDesactivar.querySelector('#btnEjecutarDesactivarCat');
                btnConfirmar.setAttribute('href', url);
            });
        }

        // Modal Reactivar
        var modalReactivar = document.getElementById('modalReactivarCategoria');
        if (modalReactivar) {
            modalReactivar.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var btnConfirmar = modalReactivar.querySelector('#btnEjecutarReactivarCat');
                btnConfirmar.setAttribute('href', url);
            });
        }
    });
</script>

<?= $this->endSection() ?>