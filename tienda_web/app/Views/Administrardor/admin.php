<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i>Resumen del Negocio</h3>
        <p class="text-muted">Bienvenido a tu panel de control, aquí tienes las estadísticas al día de hoy.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-success border-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Ingresos Totales</div>
                        <div class="h5 mb-0 fw-bold text-dark">$<?= number_format($ingresos, 2) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-warning border-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Órdenes Pendientes</div>
                        <div class="h5 mb-0 fw-bold text-dark"><?= $ordenes_pendientes ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-primary border-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Equipos en Venta</div>
                        <div class="h5 mb-0 fw-bold text-dark"><?= $productos_activos ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-mobile-alt fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-info border-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Clientes Registrados</div>
                        <div class="h5 mb-0 fw-bold text-dark"><?= $clientes_total ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>Últimas Ventas</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 1;">
                            <tr>
                                <th class="px-4 py-3"># Pedido</th>
                                <th class="py-3">Cliente</th>
                                <th class="py-3">Fecha</th>
                                <th class="py-3">Total</th>
                                <th class="py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($ordenes_recientes)): ?>
                                <?php foreach($ordenes_recientes as $orden): ?>
                                    <tr>
                                        <td class="px-4 fw-bold text-secondary py-3">#<?= str_pad($orden['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="py-3"><?= $orden['nombre'] . ' ' . $orden['apellidos'] ?></td>
                                        <td class="py-3"><?= date('d/m/Y H:i', strtotime($orden['fecha'])) ?></td>
                                        <td class="fw-bold text-success py-3">$<?= number_format($orden['total'], 2) ?></td>
                                        <td class="py-3">
                                            <?php if($orden['estado'] == 'pendiente'): ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>
                                            <?php elseif($orden['estado'] == 'pagado'): ?>
                                                <span class="badge bg-info"><i class="fas fa-check me-1"></i>Pagado</span>
                                            <?php elseif($orden['estado'] == 'enviado'): ?>
                                                <span class="badge bg-primary"><i class="fas fa-truck me-1"></i>Enviado</span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><i class="fas fa-box-open me-1"></i>Entregado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>Aún no hay ventas registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-headset me-2"></i>Soporte Interno</h6>
            </div>
            <div class="card-body text-center py-4">
                <?php if($chats_pendientes > 0): ?>
                    <div class="mb-3">
                        <i class="fas fa-comments text-warning fa-3x"></i>
                    </div>
                    <h5 class="fw-bold">¡Tienes <?= $chats_pendientes ?> chat(s) pendiente(s)!</h5>
                    <p class="text-muted small">Tus agentes de Atención al Cliente requieren tu ayuda.</p>
                    <a href="<?= base_url('admin/soporte') ?>" class="btn btn-warning btn-sm w-100 fw-bold text-dark">Ir a Bandeja de Entrada</a>
                <?php else: ?>
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success fa-3x opacity-50"></i>
                    </div>
                    <p class="text-muted mb-0">No hay chats de soporte pendientes. Todo en orden.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Alerta de Stock Bajo</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if(!empty($stock_bajo)): ?>
                        <?php foreach($stock_bajo as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= $item['nombre'] ?></h6>
                                    <small class="text-muted">SKU: <?= $item['sku'] ?></small>
                                </div>
                                <span class="badge bg-danger rounded-pill fs-6 px-3"><?= $item['stock'] ?> disponibles</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted py-4 border-0">
                            <i class="fas fa-box text-success mb-2 opacity-50 fa-2x"></i><br>
                            Todo tu inventario tiene buen nivel de stock.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>