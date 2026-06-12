<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Gestión de Características (Filtros)</h2>
        <p class="text-muted">Define qué características tendrán tus productos (Ej: Color, GB, Batería).</p>
    </div>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalFiltro">
        <i class="fas fa-plus me-2"></i>Nuevo Filtro
    </button>
</div>

<?php if(session()->getFlashdata('msg')):?>
    <div class="alert alert-info"><?= session()->getFlashdata('msg') ?></div>
<?php endif;?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre de la Característica</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($filtros)): ?>
                    <?php foreach ($filtros as $index => $f): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <span class="badge bg-secondary me-2">Atributo</span>
                                <strong><?= $f['nombre'] ?></strong>
                            </td>
                            <td>
                                <?php if ($f['activo'] == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="fas fa-ban me-1"></i>Desactivado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($f['activo'] == 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDesactivarFiltro" 
                                            data-url="<?= base_url('admin/filtros/eliminar/'.$f['id']) ?>">
                                        <i class="fas fa-trash-alt me-1"></i> Desactivar
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalReactivarFiltro" 
                                            data-url="<?= base_url('admin/filtros/reactivar/'.$f['id']) ?>">
                                        <i class="fas fa-check-circle me-1"></i> Reactivar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No has definido características aún. <br>
                            Empieza creando "Color" o "Almacenamiento".
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalFiltro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Nueva Característica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/filtros/guardar') ?>" method="post">
          <div class="modal-body">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Filtro</label>
                <input type="text" class="form-control" name="nombre" required placeholder="Ej: Color, Capacidad, RAM...">
                <div class="form-text">Esto aparecerá cuando crees un nuevo producto.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar Filtro</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Desactivar Filtro (Rojo) -->
<div class="modal fade" id="modalDesactivarFiltro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-circle me-2"></i> Confirmar Desactivación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-eye-slash fa-3x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">¿Ocultar este filtro?</h5>
                <p class="text-muted mb-0">Esta característica dejará de estar disponible al crear nuevos productos.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnEjecutarDesactivarFiltro" class="btn btn-danger px-4 rounded-pill fw-bold">Sí, desactivar</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reactivar Filtro (Verde) -->
<div class="modal fade" id="modalReactivarFiltro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i> Confirmar Reactivación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-eye fa-3x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">¿Reactivar este filtro?</h5>
                <p class="text-muted mb-0">Esta característica volverá a estar disponible para asignarla a nuevos productos.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnEjecutarReactivarFiltro" class="btn btn-success px-4 rounded-pill fw-bold">Sí, reactivar</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal Desactivar
        var modalDesactivar = document.getElementById('modalDesactivarFiltro');
        if (modalDesactivar) {
            modalDesactivar.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var btnConfirmar = modalDesactivar.querySelector('#btnEjecutarDesactivarFiltro');
                btnConfirmar.setAttribute('href', url);
            });
        }

        // Modal Reactivar
        var modalReactivar = document.getElementById('modalReactivarFiltro');
        if (modalReactivar) {
            modalReactivar.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var btnConfirmar = modalReactivar.querySelector('#btnEjecutarReactivarFiltro');
                btnConfirmar.setAttribute('href', url);
            });
        }
    });
</script>
<?= $this->endSection() ?>