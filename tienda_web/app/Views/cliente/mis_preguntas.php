<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="mb-4 mt-2">
        <h2 class="fw-bold text-dark mb-0">Mis Preguntas al Soporte</h2>
        <p class="text-muted small">Revisa el estado de tus consultas y las respuestas de nuestro equipo.</p>
    </div>

    <div class="card shadow-sm border-0 bg-white rounded-4">
        <div class="card-body p-4">

            <?php if (empty($salas)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-secondary fw-bold">Aún no tienes preguntas</h5>
                    <p class="text-muted">Cuando tengas dudas sobre algún equipo, envíalas desde el catálogo y aquí aparecerán.</p>
                    <a href="<?= base_url('dashboard/cliente') ?>" class="btn btn-primary mt-2 rounded-pill px-4">Ir a la Tienda</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Nº de Ticket</th>
                                <th>Fecha de Envío</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salas as $sala): ?>
                                <tr>
                                    <td class="fw-bold text-dark">#<?= esc($sala['id']) ?></td>
                                    <td><?= date('d/m/Y - h:i A', strtotime($sala['fecha_inicio'])) ?></td>
                                    <td>
                                        <?php if ($sala['estado'] == 'nuevo'): ?>
                                            <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fas fa-paper-plane me-1"></i>Enviado</span>
                                        <?php elseif ($sala['estado'] == 'en_proceso'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-search me-1"></i>Soporte Revisando</span>
                                        <?php elseif ($sala['estado'] == 'espera_cliente'): ?>
                                            <span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="fas fa-comment-dots me-1"></i>¡Tienes Respuesta!</span>
                                        <?php elseif ($sala['estado'] == 'cerrado'): ?>
                                            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i>Resuelto</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= esc($sala['estado']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('mis-preguntas/chat/' . $sala['id']) ?>"
                                           class="btn btn-sm btn-outline-dark rounded-pill fw-bold px-3">
                                            <i class="fas fa-comments me-1"></i>Leer Chat
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
