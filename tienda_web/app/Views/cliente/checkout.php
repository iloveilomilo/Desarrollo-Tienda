<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0"><i class="fas fa-lock me-2 text-success"></i> Pago Seguro</h2>
        <p class="text-muted small">Estás a un paso de estrenar tu nuevo equipo.</p>
    </div>

    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('mensaje') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('checkout/procesar') ?>" method="post">
        <div class="row">

            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="fw-bold mb-0"><i class="fas fa-truck text-primary me-2"></i> 1. Elige tu dirección de envío</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($direcciones)): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                                <strong>No tienes direcciones guardadas.</strong><br>
                                Por favor, ve a tu perfil para agregar una antes de continuar.
                                <br><br>
                                <a href="<?= base_url('perfil') ?>" class="btn btn-primary btn-sm rounded-pill px-4">Ir a Mi Perfil</a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($direcciones as $index => $dir): ?>
                                    <div class="col-md-6">
                                        <label class="w-100">
                                            <input type="radio" name="direccion_id" value="<?= $dir['id'] ?>"
                                                   class="card-input-element d-none" <?= $index === 0 ? 'checked' : '' ?> required>
                                            <div class="card card-body bg-light border-2 card-input shadow-sm h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-home me-2"></i><?= esc($dir['calle']) ?> #<?= esc($dir['numero_exterior']) ?></h6>
                                                    <i class="fas fa-check-circle text-success check-icon d-none fs-5"></i>
                                                </div>
                                                <p class="text-muted small mb-1">C.P. <?= esc($dir['codigo_postal']) ?> - Col. <?= esc($dir['colonia']) ?></p>
                                                <p class="text-muted small mb-2"><?= esc($dir['ciudad']) ?>, <?= esc($dir['estado']) ?></p>
                                                <div class="bg-white p-2 rounded small border">
                                                    <strong>Recibe:</strong> <?= esc($dir['nombre_recibe']) ?> (<?= esc($dir['telefono_recibe']) ?>)
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="fw-bold mb-0"><i class="fas fa-credit-card text-primary me-2"></i> 2. Método de Pago</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        <p class="text-muted mb-0">Serás redirigido a <strong>Mercado Pago</strong> para completar tu compra de forma 100% segura.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-light sticky-top" style="top:20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumen de tu Orden</h5>

                        <div class="mb-4" style="max-height:300px; overflow-y:auto;">
                            <?php foreach ($carrito as $item): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= base_url('uploads/productos/' . $item['imagen']) ?>"
                                         class="img-thumbnail border-0 bg-transparent me-2"
                                         style="width:50px; height:50px; object-fit:contain;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small fw-bold text-truncate" style="max-width:150px;"><?= esc($item['nombre']) ?></h6>
                                        <small class="text-muted">Cant: <?= $item['cantidad'] ?></small>
                                    </div>
                                    <div class="fw-bold small">$<?= number_format($item['precio_final'] * $item['cantidad'], 2) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Envío</span>
                            <span class="text-success fw-bold">¡Gratis!</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 pt-3 border-top border-dark">
                            <h5 class="fw-bold mb-0">Total</h5>
                            <h4 class="text-primary fw-bold mb-0">$<?= number_format($total, 2) ?></h4>
                        </div>

                        <?php if (!empty($direcciones)): ?>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill shadow">
                                    Pagar con Mercado Pago <i class="fas fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<style>
    .card-input-element:checked + .card-input { border-color: #0d6efd !important; background-color: #f8fbff !important; }
    .card-input-element:checked + .card-input .check-icon { display: block !important; }
</style>

<?= $this->endSection() ?>
