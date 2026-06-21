<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-shopping-bag me-2 text-primary"></i> Mis Compras</h2>
            <p class="text-muted small mb-0">Historial detallado de tus pedidos en NewPhoneMX.</p>
        </div>
        <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-2"></i>Volver a la Tienda
        </a>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-secondary fw-bold">Aún no tienes compras</h4>
            <p class="text-muted">¡Anímate a estrenar un equipo hoy mismo!</p>
            <div class="mt-2">
                <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-primary rounded-pill px-4">Explorar Catálogo</a>
            </div>
        </div>
    <?php else: ?>
        <div class="accordion shadow-sm rounded-4 overflow-hidden" id="accordionCompras">
            <?php foreach ($pedidos as $index => $p): ?>
                <div class="accordion-item border-0 mb-2 rounded-4 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?> bg-white py-4"
                                type="button" data-bs-toggle="collapse" data-bs-target="#pedido<?= $p['id'] ?>">
                            <div class="row w-100 align-items-center">
                                <div class="col-md-3">
                                    <span class="text-muted small d-block text-uppercase fw-bold">Fecha de Compra</span>
                                    <span class="fw-bold text-dark"><?= date('d M Y, H:i', strtotime($p['fecha'])) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted small d-block text-uppercase fw-bold">Total</span>
                                    <span class="fw-bold text-success">$<?= number_format($p['total'], 2) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted small d-block text-uppercase fw-bold">Estatus</span>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3">
                                        <i class="fas fa-check-circle me-1"></i> <?= strtoupper($p['estado']) ?>
                                    </span>
                                </div>
                                <div class="col-md-3 text-end pe-4">
                                    <span class="text-primary fw-bold small">Ver detalles <i class="fas fa-chevron-down ms-1"></i></span>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="pedido<?= $p['id'] ?>"
                         class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                         data-bs-parent="#accordionCompras">
                        <div class="accordion-body bg-light p-4">
                            <div class="row">
                                <div class="col-md-7">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Productos en este pedido</h6>
                                    <?php foreach ($p['productos'] as $prod): ?>
                                        <div class="d-flex align-items-center mb-3 bg-white p-2 rounded-3 border">
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 fw-bold small"><?= esc($prod['producto_nombre']) ?></h6>
                                                <small class="text-muted"><?= esc($prod['marca']) ?> | Cant: <?= $prod['cantidad'] ?></small>
                                            </div>
                                            <div class="text-end fw-bold text-dark me-2">
                                                $<?= number_format($prod['precio_unitario'] * $prod['cantidad'], 2) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-md-5">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Dirección de Entrega</h6>
                                    <div class="card card-body border-0 shadow-sm rounded-3">
                                        <p class="mb-1 fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i><?= esc($p['calle']) ?> #<?= esc($p['numero_exterior']) ?></p>
                                        <p class="text-muted small mb-0"><?= esc($p['ciudad']) ?></p>
                                        <hr class="my-2">
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Tu pedido está siendo procesado.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .accordion-button:not(.collapsed) { background-color: #fff; color: inherit; box-shadow: none; }
    .accordion-button::after { display: none; }
</style>

<?= $this->endSection() ?>
