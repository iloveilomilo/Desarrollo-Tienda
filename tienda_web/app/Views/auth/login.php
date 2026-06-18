<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .card-login {
        width: 100%;
        max-width: 420px;
        border-radius: 15px;
        border: none;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .card-login .form-control {
        border-radius: 10px;
        padding: 10px 15px;
    }

    .btn-login {
        background-color: #764ba2;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        color: #fff;
    }

    .btn-login:hover {
        background-color: #5c3882;
        color: #fff;
    }

    .link-recuperar {
        color: #764ba2;
        text-decoration: none;
        font-size: 0.9em;
    }

    .link-recuperar:hover {
        text-decoration: underline;
    }
</style>

<div class="d-flex align-items-center justify-content-center" style="min-height: 78vh;">
    <div class="card card-login p-4 bg-white">

        <div class="text-center mb-4">
            <?php if (session()->getFlashdata('alerta_intruso')): ?>
                <div class="alert alert-danger alert-dismissible fade show fw-bold text-center shadow-sm mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('alerta_intruso') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <h3 class="fw-bold" style="color:#764ba2">NewPhoneMX</h3>
            <p class="text-secondary">Bienvenido de nuevo</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success text-center rounded-3">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert alert-danger text-center rounded-3">
                <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/auth/login') ?>" method="post">
            <div class="mb-3">
                <label for="email" class="form-label text-muted small fw-bold">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nombre@correo.com" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-muted small fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-login py-2">Iniciar Sesión</button>
            </div>
            <div class="text-center mt-2 mb-3">
                <a href="#" class="link-recuperar" data-bs-toggle="modal" data-bs-target="#modalRecuperarPass">
                    ¿Olvidaste tu contraseña o tu cuenta está bloqueada?
                </a>
            </div>
            <div class="text-center mt-4">
                <a href="<?= base_url('registro') ?>" class="btn btn-outline-secondary w-100 py-2 rounded-pill fw-semibold">Crear cuenta nueva</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Recuperar Contraseña -->
<div class="modal fade" id="modalRecuperarPass" tabindex="-1" aria-labelledby="modalRecuperarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#764ba2; color:white;">
                <h5 class="modal-title" id="modalRecuperarLabel">Recuperar Acceso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="body-paso-1">
                <p class="text-muted small">Ingresa tu correo y el teléfono móvil asociado a tu cuenta para validar tu identidad.</p>
                <form id="form-recuperar-datos">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="rec_correo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono Móvil</label>
                        <input type="text" class="form-control" id="rec_telefono" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn text-white" style="background-color:#764ba2;" id="btn-validar-datos">
                            Validar Datos
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-body d-none" id="body-paso-2">
                <div class="alert alert-success small">Te hemos enviado un código de 6 dígitos a tu correo.</div>
                <form id="form-recuperar-codigo">
                    <div class="mb-3">
                        <label class="form-label">Código de Verificación</label>
                        <input type="number" class="form-control" id="rec_codigo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="rec_nueva_pass" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn text-white" style="background-color:#764ba2;" id="btn-cambiar-pass">
                            Cambiar Contraseña y Desbloquear
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        document.getElementById('form-recuperar-datos').addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btn-validar-datos');
            btn.innerHTML = 'Validando y enviando correo...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('correo', document.getElementById('rec_correo').value);
            formData.append('telefono', document.getElementById('rec_telefono').value);

            fetch('<?= base_url('auth/solicitar_recuperacion') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('body-paso-1').classList.add('d-none');
                        document.getElementById('body-paso-2').classList.remove('d-none');

                        Swal.fire({
                            icon: 'success',
                            title: '¡Código enviado!',
                            text: 'Revisa tu bandeja de entrada o la carpeta de SPAM.',
                            confirmButtonColor: '#764ba2'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.msg,
                            confirmButtonColor: '#764ba2'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'Hubo un problema al contactar con el servidor.',
                        confirmButtonColor: '#764ba2'
                    });
                })
                .finally(() => {
                    btn.innerHTML = 'Validar Datos';
                    btn.disabled = false;
                });
        });

        document.getElementById('form-recuperar-codigo').addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btn-cambiar-pass');
            btn.innerHTML = 'Guardando...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('correo', document.getElementById('rec_correo').value);
            formData.append('codigo', document.getElementById('rec_codigo').value);
            formData.append('nueva_password', document.getElementById('rec_nueva_pass').value);

            fetch('<?= base_url('auth/restablecer_password') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Cuenta recuperada!',
                            text: data.msg,
                            confirmButtonColor: '#764ba2',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg,
                            confirmButtonColor: '#764ba2'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado al actualizar la contraseña.',
                        confirmButtonColor: '#764ba2'
                    });
                })
                .finally(() => {
                    btn.innerHTML = 'Cambiar Contraseña y Desbloquear';
                    btn.disabled = false;
                });
        });

    });
</script>

<?= $this->endSection() ?>
