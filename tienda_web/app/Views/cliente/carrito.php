<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i> Mi Carrito</h2>
        <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
        </a>
    </div>

    <div class="row">
        <?php if (empty($carrito)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-5 shadow-sm bg-white border-0 rounded">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>Tu carrito está vacío</h4>
                    <p class="text-muted">¡Anímate a explorar nuestro catálogo de celulares!</p>
                    <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-primary mt-2">Ver Catálogo</a>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4">Producto</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrito as $item): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= base_url('uploads/productos/' . $item['imagen']) ?>"
                                                         alt="<?= esc($item['nombre']) ?>"
                                                         class="img-thumbnail border-0 me-3"
                                                         style="width:60px; height:60px; object-fit:contain;">
                                                    <a href="<?= base_url('dashboard/cliente?ver_producto=' . $item['inventario_id']) ?>"
                                                       class="fw-bold text-primary text-decoration-none">
                                                        <?= esc($item['nombre']) ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="text-center text-muted">$<?= number_format($item['precio'], 2) ?></td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm mx-auto" style="width:100px;">
                                                    <button class="btn btn-outline-secondary btn-update-cart"
                                                            data-id="<?= $item['id'] ?>" data-action="minus" type="button">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="text" class="form-control text-center fw-bold px-0 bg-white"
                                                           value="<?= $item['cantidad'] ?>" readonly>
                                                    <button class="btn btn-outline-secondary btn-update-cart"
                                                            data-id="<?= $item['id'] ?>" data-action="plus" type="button">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-primary pe-4">
                                                $<?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('carrito/eliminar/' . $item['id']) ?>"
                                                   class="btn btn-danger btn-sm rounded-circle" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded bg-light">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Resumen de Compra</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal (<?= array_sum(array_column($carrito, 'cantidad')) ?> artículos)</span>
                            <span class="fw-bold">$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Envío</span>
                            <span class="text-success fw-bold">¡Gratis!</span>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold mb-0">Total a Pagar</h5>
                            <h4 class="text-primary fw-bold mb-0">$<?= number_format($total, 2) ?></h4>
                        </div>
                        <div class="d-grid">
                            <a href="<?= base_url('checkout') ?>" class="btn btn-success btn-lg fw-bold">
                                <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-update-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const formData = new FormData();
            formData.append('id', this.getAttribute('data-id'));
            formData.append('accion', this.getAttribute('data-action'));

            fetch('<?= base_url('carrito/actualizar') ?>', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { location.reload(); }
                    else { Swal.fire('Atención', data.message || 'No se pudo actualizar.', 'warning'); }
                });
        });
    });
});
</script>

<?= $this->endSection() ?>
