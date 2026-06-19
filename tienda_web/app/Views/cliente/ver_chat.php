<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h2 class="fw-bold text-dark mb-0">Ticket #<?= esc($sala['id']) ?></h2>
            <p class="text-muted small">Estado actual: <span class="fw-bold text-primary text-uppercase"><?= esc($sala['estado']) ?></span></p>
        </div>
        <a href="<?= base_url('mis-preguntas') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Volver al historial
        </a>
    </div>

    <div class="card shadow-sm border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-body p-4" style="height:50vh; overflow-y:auto; background-color:#f8f9fa;" id="chatBox">
            <?php foreach ($mensajes as $msg): ?>
                <?php $esMio = ($msg['remitente_id'] == session('id')); ?>
                <div class="d-flex w-100 mb-3 <?= $esMio ? 'justify-content-end' : 'justify-content-start' ?>">
                    <div class="p-3 rounded-4 shadow-sm"
                         style="max-width:75%; <?= $esMio
                             ? 'background-color:#0d6efd; color:white; border-bottom-right-radius:0 !important;'
                             : 'background-color:white; color:#333; border-bottom-left-radius:0 !important;' ?>">
                        <p class="mb-1" style="white-space:pre-wrap;"><?= esc($msg['mensaje']) ?></p>
                        <small class="<?= $esMio ? 'text-white-50' : 'text-muted' ?>" style="font-size:0.70rem;">
                            <i class="fas fa-clock me-1"></i><?= date('d M, h:i A', strtotime($msg['fecha_envio'])) ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($sala['estado'] != 'cerrado'): ?>
            <div class="card-footer bg-white border-top p-3">
                <form action="<?= base_url('mis-preguntas/responder') ?>" method="post" class="d-flex align-items-center">
                    <input type="hidden" name="sala_id" value="<?= esc($sala['id']) ?>">
                    <input type="text" name="mensaje"
                           class="form-control rounded-pill border-light bg-light px-4 me-2"
                           placeholder="Escribe tu respuesta aquí..." required autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="card-footer bg-light border-top p-3 text-center text-muted">
                <i class="fas fa-lock me-2"></i>Este ticket ha sido cerrado por el equipo de soporte.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatBox = document.getElementById("chatBox");
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>

<?= $this->endSection() ?>
