<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark"><i class="fas fa-history text-secondary me-2"></i>Historial de Atenciones</h3>
        <p class="text-muted">Consultas cerradas y resueltas.</p>
    </div>
    <a href="<?= base_url('soporte/mensajes') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
        <i class="fas fa-inbox me-2"></i>Bandeja de Entrada
    </a>
</div>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-success border-0 shadow-sm">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('msg') ?>
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
                        <th>Fecha de Apertura</th>
                        <th>Fecha de Cierre</th>
                        <th class="text-center pe-4">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conversaciones)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block text-light"></i>
                                <h5>Sin historial aún</h5>
                                <p>Aquí aparecerán las conversaciones que hayas cerrado.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($conversaciones as $conv): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= str_pad($conv['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center me-2 fw-bold"
                                             style="width:35px; height:35px;">
                                            <?= strtoupper(substr($conv['nombre'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span class="text-dark"><?= esc($conv['nombre'] . ' ' . $conv['apellidos']) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($conv['fecha_inicio'])) ?></td>
                                <td class="text-muted small">
                                    <?= !empty($conv['fecha_cierre']) ? date('d/m/Y H:i', strtotime($conv['fecha_cierre'])) : '—' ?>
                                </td>
                                <td class="text-center pe-4">
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i>Resuelto
                                    </span>
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
