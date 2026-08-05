<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-primary"><i class="fas fa-user-shield me-2"></i>Chats con Administradores</h3>
        <p class="text-muted">Habla con cualquier administrador cuando necesites resolver una duda mayor.</p>
    </div>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i><?= session()->getFlashdata('msg') ?>
    </div>
<?php endif; ?>

<div class="row">

    <!-- ── Lista de administradores ── -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0" style="height:650px;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-users me-2"></i>Administradores</h6>
            </div>

            <div class="list-group list-group-flush" style="overflow-y:auto; flex:1;">
                <?php if (!empty($admins)): ?>
                    <?php foreach ($admins as $a): ?>
                        <?php
                        $isActive = (isset($admin_actual) && $admin_actual['id'] == $a['id'])
                            ? 'bg-primary bg-opacity-10 border-start border-primary border-4'
                            : '';
                        // Ojo: aquí la agente es quien pregunta ("cliente" en la tabla) y el
                        // admin es quien "atiende", así que los estados se leen al revés que
                        // en la bandeja de tickets de clientes: 'nuevo' significa que YA
                        // respondiste y le toca al admin; 'espera_cliente' significa que el
                        // admin ya contestó y ahora te toca a ti.
                        $badgeEstado = match($a['estado']) {
                            'nuevo'          => '<span class="badge bg-info ms-1">Respondido</span>',
                            'en_proceso'     => '<span class="badge bg-info ms-1">Respondido</span>',
                            'espera_cliente' => '<span class="badge bg-danger ms-1">¡Tienes respuesta!</span>',
                            'cerrado'        => '<span class="badge bg-secondary ms-1">Cerrado</span>',
                            default          => '<span class="badge bg-light text-muted border ms-1">Sin conversación</span>'
                        };
                        ?>
                        <a href="<?= base_url('soporte/admins/chat/' . $a['id']) ?>"
                            class="list-group-item list-group-item-action py-3 <?= $isActive ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 fw-bold text-dark">
                                    <i class="fas fa-user-shield text-primary me-1"></i>
                                    <?= esc($a['nombre'] . ' ' . $a['apellidos']) ?>
                                </h6>
                                <?php if ($a['fecha_inicio']): ?>
                                    <small class="text-muted" style="font-size:0.75rem;">
                                        <?= date('d M', strtotime($a['fecha_inicio'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <p class="mb-0 small text-secondary">
                                <?= $badgeEstado ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-user-slash fa-3x mb-3 text-light d-block"></i>
                        <p>No hay administradores activos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Panel de chat ── -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 d-flex flex-column" style="height:650px;">

            <?php if (isset($admin_actual)): ?>

                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-comments me-2"></i><?= esc($admin_actual['nombre'] . ' ' . $admin_actual['apellidos']) ?>
                        </h6>
                        <small class="text-muted">
                            <?= $sala_actual ? 'Sala de soporte #' . $sala_actual['id'] : 'Aún no tienen conversación' ?>
                        </small>
                    </div>
                </div>

                <div class="card-body" id="chatContainer" style="overflow-y:auto; background-color:#f8f9fa; flex:1;">
                    <?php if (!empty($mensajes)): ?>
                        <?php foreach ($mensajes as $m): ?>

                            <?php if ($m['remitente_id'] == session('id')): ?>
                                <!-- Mensaje propio (derecha) -->
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="bg-primary text-white p-3 shadow-sm"
                                        style="max-width:75%; border-radius:15px; border-bottom-right-radius:0;">
                                        <p class="mb-1" style="line-height:1.4;"><?= nl2br(esc($m['mensaje'])) ?></p>
                                        <small class="text-white-50 d-block text-end" style="font-size:0.7rem;">
                                            <?= date('H:i', strtotime($m['fecha_envio'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Mensaje del administrador (izquierda) -->
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="bg-white text-dark p-3 shadow-sm border"
                                        style="max-width:75%; border-radius:15px; border-bottom-left-radius:0;">
                                        <div class="fw-bold text-primary mb-1" style="font-size:0.85rem;">
                                            <?= esc($m['remitente']) ?>
                                            <span class="text-muted fw-normal">(Admin)</span>
                                        </div>
                                        <p class="mb-1 text-secondary" style="line-height:1.4;"><?= nl2br(esc($m['mensaje'])) ?></p>
                                        <small class="text-muted d-block text-end mt-1" style="font-size:0.7rem;">
                                            <?= date('H:i', strtotime($m['fecha_envio'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="d-flex h-100 justify-content-center align-items-center text-muted">
                            <p class="bg-white px-4 py-2 rounded-pill shadow-sm border small">
                                Aún no hay mensajes con este administrador. Escribe algo para iniciar la conversación.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!$sala_actual || $sala_actual['estado'] != 'cerrado'): ?>
                    <div class="card-footer bg-white border-top p-3">
                        <form action="<?= base_url('soporte/admins/responder') ?>" method="post">
                            <input type="hidden" name="admin_id" value="<?= $admin_actual['id'] ?>">
                            <div class="input-group shadow-sm rounded">
                                <textarea name="mensaje" class="form-control bg-light border-0 py-3 px-4"
                                    rows="1" placeholder="Escribe tu mensaje para el administrador..." required
                                    style="resize:none; border-radius:25px 0 0 25px;"></textarea>
                                <button class="btn btn-primary px-4" type="submit"
                                    style="border-radius:0 25px 25px 0;">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card-footer bg-light text-center text-muted p-4">
                        <i class="fas fa-lock text-secondary mb-2 fa-2x d-block"></i>
                        <p class="mb-0">Esta consulta ha sido marcada como resuelta y el chat está cerrado.</p>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted bg-light rounded">
                    <div class="bg-white p-4 rounded-circle shadow-sm mb-3">
                        <i class="fas fa-comments text-primary fa-4x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Chats con Administradores</h5>
                    <p class="text-center px-5">
                        Selecciona un administrador de la lista para ver su conversación
                        o escribirle algo nuevo, aunque nunca hayan hablado antes.
                    </p>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatContainer = document.getElementById("chatContainer");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>

<?= $this->endSection() ?>
