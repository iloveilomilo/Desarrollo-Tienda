<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark"><i class="fas fa-headset text-primary me-2"></i>Panel de Atención al Cliente</h3>
        <p class="text-muted">Bienvenida, aquí tienes un resumen de los tickets activos.</p>
        <!-- Hoy es 23 de de junio del 2026 qqq-->
    </div>
    <a href="<?= base_url('soporte/mensajes') ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
        <i class="fas fa-inbox me-2"></i>Ver Bandeja de Entrada
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-danger border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Tickets Nuevos</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?= $nuevos ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-warning border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">En Atención</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?= $en_proceso ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-success border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Resueltos</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?= $resueltos ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-5">
                <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">Bandeja de Entrada</h5>
                <p class="text-muted small">Consultas abiertas de los clientes esperando respuesta.</p>
                <a href="<?= base_url('soporte/mensajes') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-inbox me-2"></i>Ver Mensajes
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-3x text-secondary mb-3"></i>
                <h5 class="fw-bold">Historial de Atenciones</h5>
                <p class="text-muted small">Consultas cerradas y resueltas anteriormente.</p>
                <a href="<?= base_url('soporte/historial') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-archive me-2"></i>Ver Historial
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
