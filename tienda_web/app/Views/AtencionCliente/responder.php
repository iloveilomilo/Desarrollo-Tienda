<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark"><i class="fas fa-comments text-primary me-2"></i>
            Conversación con <?= esc($conversacion['nombre'] . ' ' . $conversacion['apellidos']) ?>
        </h3>
        <small class="text-muted">Sala #<?= esc($conversacion['id']) ?></small>
    </div>
    <a href="<?= base_url('soporte/mensajes') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="fas fa-arrow-left me-2"></i>Volver a Bandeja
    </a>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i><?= session()->getFlashdata('msg') ?>
    </div>
<?php endif; ?>

<div class="row">

    <!-- Panel de chat -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 d-flex flex-column" style="height:580px;">
            <div class="card-header bg-primary text-white py-3">
                <strong><i class="fas fa-comment-dots me-2"></i>Chat Activo</strong>
            </div>

            <div class="card-body p-3" id="caja-mensajes"
                 style="height:430px; overflow-y:auto; background-color:#f8f9fa;">
                <div class="text-center mt-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando mensajes...</p>
                </div>
            </div>

            <?php if ($conversacion['estado'] !== 'cerrado'): ?>
                <div class="card-footer bg-white border-top p-3">
                    <form action="<?= base_url('soporte/enviar_mensaje') ?>" method="post">
                        <input type="hidden" name="id_conversacion" value="<?= esc($conversacion['id']) ?>">
                        <div class="input-group shadow-sm">
                            <input type="text" class="form-control border-0 bg-light px-4"
                                   name="mensaje" id="inputMensaje"
                                   placeholder="Escribe tu respuesta al cliente..." required autocomplete="off"
                                   style="border-radius:25px 0 0 25px;">
                            <button class="btn btn-primary px-4" type="submit"
                                    style="border-radius:0 25px 25px 0;">
                                <i class="fas fa-paper-plane me-1"></i>Enviar
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="card-footer bg-light text-center text-muted p-3">
                    <i class="fas fa-lock me-2"></i>Esta conversación ha sido cerrada.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel de gestión -->
    <div class="col-md-4">

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-dark text-white py-3">
                <strong><i class="fas fa-sliders-h me-2"></i>Gestión del Ticket</strong>
            </div>
            <div class="card-body">
                <form action="<?= base_url('soporte/actualizar_estado') ?>" method="post">
                    <input type="hidden" name="id_conversacion" id="id_conversacion_js" value="<?= esc($conversacion['id']) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Estado actual</label>
                        <select class="form-select" name="estado">
                            <option value="nuevo" <?= $conversacion['estado'] == 'nuevo' ? 'selected' : '' ?>>
                                Nuevo
                            </option>
                            <option value="en_proceso" <?= $conversacion['estado'] == 'en_proceso' ? 'selected' : '' ?>>
                                En Proceso
                            </option>
                            <option value="espera_cliente" <?= $conversacion['estado'] == 'espera_cliente' ? 'selected' : '' ?>>
                                Espera del Cliente
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">
                        <i class="fas fa-save me-2"></i>Guardar Estado
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-danger border-0">
            <div class="card-body text-center py-4">
                <i class="fas fa-archive fa-2x text-danger mb-3"></i>
                <p class="text-muted small mb-3">Al cerrar, el ticket pasará al historial de atenciones.</p>
                <button type="button" class="btn btn-outline-danger w-100 fw-bold rounded-pill"
                        onclick="confirmarCierre()">
                    <i class="fas fa-times-circle me-2"></i>Cerrar Conversación
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const idSala = document.getElementById('id_conversacion_js').value;
const cajaMensajes = document.getElementById('caja-mensajes');

function cargarMensajes() {
    fetch('<?= base_url('soporte/mensajes_ajax/') ?>' + idSala)
        .then(r => r.json())
        .then(data => {
            const alFondo = cajaMensajes.scrollHeight - cajaMensajes.scrollTop <= cajaMensajes.clientHeight + 50;
            cajaMensajes.innerHTML = '';

            if (data.mensajes && data.mensajes.length > 0) {
                data.mensajes.forEach(msg => {
                    const esAgente = (msg.rol === 'atencion_cliente' || msg.rol === 'admin');
                    cajaMensajes.innerHTML += `
                        <div class="d-flex justify-content-${esAgente ? 'end' : 'start'} mb-3">
                            <div class="p-3 shadow-sm"
                                 style="max-width:78%; border-radius:15px;
                                        border-bottom-${esAgente ? 'right' : 'left'}-radius:0;
                                        background-color:${esAgente ? '#0d6efd' : '#ffffff'};
                                        color:${esAgente ? '#fff' : '#333'};">
                                <div class="fw-bold mb-1" style="font-size:0.80rem; opacity:0.8;">${msg.nombre}</div>
                                <p class="mb-1" style="line-height:1.4;">${msg.mensaje}</p>
                                <small style="font-size:0.70rem; opacity:0.7;">${msg.fecha_envio}</small>
                            </div>
                        </div>`;
                });
                if (alFondo) cajaMensajes.scrollTop = cajaMensajes.scrollHeight;
            } else {
                cajaMensajes.innerHTML = '<div class="text-center mt-5 text-muted"><i class="fas fa-comment-slash fa-2x mb-2 d-block"></i>Aún no hay mensajes.</div>';
            }
        });
}

function confirmarCierre() {
    Swal.fire({
        title: '¿Cerrar conversación?',
        text: 'El ticket pasará al historial y no podrá reabrirse.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('soporte/cerrar/') ?>' + idSala;
        }
    });
}

cargarMensajes();
setInterval(cargarMensajes, 3000);
</script>

<?= $this->endSection() ?>
