<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewPhoneMX</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #764ba2;
            --secondary:     #667eea;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        #sidebar-wrapper {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0,0,0,.05);
            box-shadow: 4px 0 15px rgba(0,0,0,.04);
            margin-left: calc(-1 * var(--sidebar-width));
            transition: margin .25s ease-out;
        }

        #wrapper.toggled #sidebar-wrapper { margin-left: 0; }

        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #wrapper.toggled #sidebar-wrapper { margin-left: calc(-1 * var(--sidebar-width)); }
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-link {
            display: block;
            padding: 10px 24px;
            color: #555;
            font-size: .92rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 0 50px 50px 0;
            margin: 2px 10px 2px 0;
            transition: all .25s;
        }

        .sidebar-link i { width: 22px; color: var(--secondary); }

        .sidebar-link:hover {
            background: rgba(118,75,162,.1);
            color: var(--primary);
            padding-left: 34px;
        }

        .sidebar-link.active {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(118,75,162,.3);
        }

        .sidebar-link.active i { color: #fff; }

        .sidebar-divider {
            font-size: .70rem;
            font-weight: 700;
            letter-spacing: .08rem;
            text-transform: uppercase;
            color: #aaa;
            padding: 12px 24px 4px;
        }

        .sidebar-logout {
            color: #dc3545 !important;
            border-top: 1px solid #f0f0f0;
            margin-top: 12px;
            padding-top: 12px;
        }

        /* ── Navbar ── */
        #page-content-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-navbar {
            background: rgba(255,255,255,.75) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,.5);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            object-fit: cover;
        }
    </style>
</head>

<body>
<div class="d-flex" id="wrapper">

    <!-- ══════════════════ SIDEBAR ══════════════════ -->
    <?php if (session('id')): ?>
    <div id="sidebar-wrapper">

        <div class="sidebar-brand text-center border-bottom">
            <i class="fas fa-mobile-alt me-2"></i>NewPhoneMX
        </div>

        <nav class="pt-2">

            <!-- Tienda (todos los roles) -->
            <a href="<?= base_url('/') ?>" class="sidebar-link <?= (current_url() == base_url('/') || current_url() == base_url('dashboard/cliente')) ? 'active' : '' ?>">
                <i class="fas fa-store me-2"></i>Tienda
            </a>

            <?php $rol = session('rol'); ?>

            <!-- ── Cliente ── -->
            <?php if ($rol === 'cliente'): ?>
                <a href="<?= base_url('carrito') ?>" class="sidebar-link <?= str_contains(current_url(), 'carrito') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart me-2"></i>Mi Carrito
                </a>
                <a href="<?= base_url('mis-compras') ?>" class="sidebar-link <?= str_contains(current_url(), 'mis-compras') ? 'active' : '' ?>">
                    <i class="fas fa-receipt me-2"></i>Mis Compras
                </a>
                <a href="<?= base_url('mis-preguntas') ?>" class="sidebar-link <?= str_contains(current_url(), 'mis-preguntas') ? 'active' : '' ?>">
                    <i class="fas fa-question-circle me-2"></i>Mis Preguntas
                </a>
                <a href="<?= base_url('perfil') ?>" class="sidebar-link <?= str_contains(current_url(), 'perfil') ? 'active' : '' ?>">
                    <i class="fas fa-user me-2"></i>Mi Perfil
                </a>
            <?php endif; ?>

            <!-- ── Administrador ── -->
            <?php if ($rol === 'admin'): ?>
                <div class="sidebar-divider">Administración</div>
                <a href="<?= base_url('admin/panel') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/panel') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="<?= base_url('admin/productos') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/productos') ? 'active' : '' ?>">
                    <i class="fas fa-boxes me-2"></i>Inventario
                </a>
                <a href="<?= base_url('admin/categorias') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/categorias') ? 'active' : '' ?>">
                    <i class="fas fa-tags me-2"></i>Categorías
                </a>
                <a href="<?= base_url('admin/filtros') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/filtros') ? 'active' : '' ?>">
                    <i class="fas fa-filter me-2"></i>Filtros
                </a>
                <a href="<?= base_url('admin/usuarios') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/usuarios') ? 'active' : '' ?>">
                    <i class="fas fa-users me-2"></i>Usuarios
                </a>
                <div class="sidebar-divider">Soporte</div>
                <a href="<?= base_url('admin/soporte') ?>" class="sidebar-link <?= str_contains(current_url(), 'admin/soporte') ? 'active' : '' ?>">
                    <i class="fas fa-envelope me-2"></i>Mensajes
                </a>
            <?php endif; ?>

            <!-- ── Atención al Cliente ── -->
            <?php if ($rol === 'atencion_cliente'): ?>
                <div class="sidebar-divider">Soporte</div>
                <a href="<?= base_url('soporte/soporte') ?>" class="sidebar-link <?= str_contains(current_url(), 'soporte/soporte') ? 'active' : '' ?>">
                    <i class="fas fa-headset me-2"></i>Panel Soporte
                </a>
                <a href="<?= base_url('soporte/mensajes') ?>" class="sidebar-link <?= str_contains(current_url(), 'mensajes') ? 'active' : '' ?>">
                    <i class="fas fa-envelope me-2"></i>Mensajes
                </a>
                <a href="<?= base_url('soporte/historial') ?>" class="sidebar-link <?= str_contains(current_url(), 'historial') ? 'active' : '' ?>">
                    <i class="fas fa-history me-2"></i>Historial
                </a>
            <?php endif; ?>

            <!-- Cerrar sesión (todos) -->
            <a href="<?= base_url('logout') ?>" class="sidebar-link sidebar-logout">
                <i class="fas fa-power-off me-2"></i>Cerrar Sesión
            </a>

        </nav>
    </div>
    <?php endif; ?>

    <!-- ══════════════════ CONTENIDO PRINCIPAL ══════════════════ -->
    <div id="page-content-wrapper">

        <!-- Navbar superior -->
        <nav class="navbar top-navbar navbar-expand-lg navbar-light px-4 py-3">
            <div class="d-flex align-items-center">
                <?php if (session('id')): ?>
                    <i class="fas fa-bars fs-5 me-3 text-secondary" id="menu-toggle" role="button"></i>
                    <span class="fw-bold fs-5 text-dark">Panel de Control</span>
                <?php else: ?>
                    <a href="<?= base_url('/') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                        <i class="fas fa-mobile-alt fs-4" style="color:var(--primary)"></i>
                        <span class="fw-bold fs-4 text-uppercase" style="color:var(--primary);letter-spacing:1px">NewPhoneMX</span>
                    </a>
                <?php endif; ?>
            </div>

            <button class="navbar-toggler border-0 ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navTop">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navTop">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <?php if (session('id')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold text-dark d-flex align-items-center gap-2"
                               href="#" data-bs-toggle="dropdown">
                                <?php $foto = session('foto_perfil') ?: 'default.png'; ?>
                                <img src="<?= base_url('uploads/perfiles/' . $foto) ?>"
                                     class="rounded-circle border border-2 user-avatar" alt="avatar">
                                <?= esc(session('nombre')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="<?= base_url(in_array(session('rol'), ['admin','atencion_cliente']) ? 'admin/perfil' : 'perfil') ?>">
                                        <i class="fas fa-user me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-primary fw-semibold px-4 rounded-pill">
                                Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('registro') ?>" class="btn btn-primary fw-semibold px-4 rounded-pill"
                               style="background:var(--primary);border-color:var(--primary)">
                                Crear Cuenta
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <!-- Sección dinámica de cada vista -->
        <main class="container-fluid px-4 py-4">
            <?= $this->renderSection('content') ?>
        </main>

    </div><!-- /page-content-wrapper -->
</div><!-- /wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const toggleBtn = document.getElementById('menu-toggle');
    const wrapper   = document.getElementById('wrapper');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => wrapper.classList.toggle('toggled'));
    }
</script>

</body>
</html>
