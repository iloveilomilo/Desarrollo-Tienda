<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5 text-center" style="max-width:600px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-success text-white py-4 border-0">
            <i class="fas fa-check-circle fa-4x mb-3"></i>
            <h2 class="fw-bold mb-0">¡Pago Exitoso!</h2>
        </div>
        <div class="card-body p-5 bg-white">
            <h5 class="text-dark mb-4">Tu compra se ha procesado correctamente.</h5>

            <div class="bg-light p-4 rounded-3 text-start mb-4 border">
                <p class="mb-2"><span class="text-muted"><i class="fas fa-hashtag me-2"></i>Nº de Orden:</span> <strong class="float-end">#<?= esc($pedido_id) ?></strong></p>
                <p class="mb-0"><span class="text-muted"><i class="fas fa-receipt me-2"></i>Transacción MP:</span> <strong class="float-end"><?= esc($transaccion) ?></strong></p>
            </div>

            <p class="text-muted">Hemos enviado un recibo detallado al correo electrónico <strong>asociado a tu cuenta</strong> con toda la información de tu envío.</p>

            <div class="mt-4 d-grid gap-2">
                <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-primary btn-lg rounded-pill fw-bold">Seguir Comprando</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
    });
</script>

<?= $this->endSection() ?>
