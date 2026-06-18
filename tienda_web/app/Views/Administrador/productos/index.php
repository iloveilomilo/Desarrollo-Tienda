<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Inventario de Productos</h2>
    <a href="<?= base_url('admin/productos/crear') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Producto
    </a>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Condición</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td>
                                    <img src="<?= base_url('uploads/productos/' . $prod['imagen_principal']) ?>"
                                        alt="img" width="50" class="rounded">
                                </td>
                                <td>
                                    <strong><?= esc($prod['nombre']) ?></strong><br>
                                    <small class="text-muted">SKU: <?= esc($prod['sku'] ?? 'N/A') ?></small>
                                </td>
                                <td>
                                    <?php if ($prod['condicion'] == 'nuevo'): ?>
                                        <span class="badge bg-success">Nuevo</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Reacondicionado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($prod['descuento']) && $prod['descuento'] > 0): ?>
                                        <?php $precioFinal = $prod['precio'] - ($prod['precio'] * ($prod['descuento'] / 100)); ?>
                                        <span class="text-decoration-line-through text-muted small">$<?= number_format($prod['precio'], 2) ?></span><br>
                                        <strong class="text-success">$<?= number_format($precioFinal, 2) ?></strong>
                                    <?php else: ?>
                                        <strong>$<?= number_format($prod['precio'], 2) ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($prod['descuento']) && $prod['descuento'] > 0): ?>
                                        <span class="badge bg-danger">-<?= $prod['descuento'] ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">0%</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($prod['stock'] < 5): ?>
                                        <span class="text-danger fw-bold"><?= $prod['stock'] ?> (Bajo)</span>
                                    <?php else: ?>
                                        <?= $prod['stock'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/productos/editar/' . $prod['id']) ?>"
                                        class="btn btn-sm btn-outline-primary" title="Editar Producto">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalConfirmarBaja"
                                        data-url="<?= base_url('admin/productos/eliminar/' . $prod['id']) ?>"
                                        title="Dar de baja">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-boxes fa-3x mb-3 d-block text-light"></i>
                                No hay productos en el inventario. <a href="<?= base_url('admin/productos/crear') ?>">Agregar el primero</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Confirmar Baja -->
<div class="modal fade" id="modalConfirmarBaja" tabindex="-1" aria-labelledby="modalBajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="modalBajaLabel"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Baja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">¿Estás seguro de dar de baja este producto?</h5>
                <p class="text-muted mb-0">El equipo ya no aparecerá en la tienda pública, pero se mantendrá en tu base de datos.</p>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnEjecutarBaja" class="btn btn-danger px-4 rounded-pill fw-bold">Sí, dar de baja</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalBaja = document.getElementById('modalConfirmarBaja');
        if (modalBaja) {
            modalBaja.addEventListener('show.bs.modal', function(event) {
                document.getElementById('btnEjecutarBaja').setAttribute('href', event.relatedTarget.getAttribute('data-url'));
            });
        }
    });
</script>

<?= $this->endSection() ?>
