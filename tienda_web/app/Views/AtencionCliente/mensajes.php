<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark"><i class="fas fa-inbox text-primary me-2"></i>Bandeja de Entrada</h3>
        <p class="text-muted">Consultas abiertas de los clientes.</p>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2"><?= count($conversaciones) ?> Tickets Abiertos</span>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i><?= session()->getFlashdata('msg') ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Ticket</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conversaciones)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                                <h5>¡Todo al día!</h5>
                                <p>No tienes mensajes pendientes por responder.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($conversaciones as $conv): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= str_pad($conv['id'], 4, '0', STR_PAD_LEFT) ?></td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2 fw-bold"
                                             style="width:35px; height:35px;">
                                            <?= strtoupper(substr($conv['nombre'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span class="fw-bold text-dark"><?= esc($conv['nombre'] . ' ' . $conv['apellidos']) ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($conv['estado'] == 'nuevo'): ?>
                                        <span class="badge bg-danger p-2"><i class="fas fa-star me-1"></i>Nuevo</span>
                                    <?php elseif ($conv['estado'] == 'en_proceso'): ?>
                                        <span class="badge bg-warning text-dark p-2"><i class="fas fa-tools me-1"></i>En Proceso</span>
                                    <?php elseif ($conv['estado'] == 'espera_cliente'): ?>
                                        <span class="badge bg-info text-dark p-2"><i class="fas fa-user-clock me-1"></i>Espera Cliente</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary p-2"><?= esc($conv['estado']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-muted small">
                                    <?= date('d/m/Y h:i A', strtotime($conv['fecha_inicio'])) ?>
                                </td>

                                <td class="text-center pe-4">
                                    <a href="<?= base_url('soporte/responder/' . $conv['id']) ?>"
                                       class="btn btn-sm btn-primary fw-bold shadow-sm rounded-pill px-3">
                                        <i class="fas fa-reply me-1"></i>Atender
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
