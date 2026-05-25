<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - NewPhoneMX</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #654696;
            --bg-light: #eaf0f7;
        }
        body { 
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .card-registro {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
        }
        .btn-primary { 
            background-color: var(--primary-color); 
            border: none; 
            border-radius: 50px;
        }
        .btn-primary:hover { 
            background-color: #52397a; 
        }
        .btn-outline-secondary {
            border-radius: 50px;
            font-weight: 600;
        }
        .text-primary-custom {
            color: var(--primary-color) !important;
        }
    </style>
  </head>
  <body>

    <div class="card card-registro p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary-custom">Únete a NewPhoneMX</h3>
            <p class="text-secondary">Crea tu cuenta para comprar</p>
        </div>
        
        <?php if(session()->getFlashdata('error')):?>
            <div class="alert alert-danger text-center rounded-3">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif;?>

        <form id="formRegistro">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-bold">Nombre(s)</label>
                    <input type="text" name="nombre" class="form-control" required autofocus>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-bold">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Teléfono (Opcional)</label>
                <input type="tel" name="telefono" class="form-control" placeholder="10 dígitos">
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2 fw-bold">Registrarme</button>
                <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary mt-2 py-2">Ya tengo cuenta. Iniciar Sesión</a>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modalVerificacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold text-primary-custom w-100 text-center">Verifica tu Correo</h5>
          </div>
          <div class="modal-body text-center pb-4">
            <p class="text-muted small">Hemos enviado un código de 6 dígitos a tu correo. Por favor, ingrésalo a continuación.</p>
            <input type="text" id="codigoVerificacion" class="form-control text-center fw-bold fs-4 mb-3" placeholder="------" maxlength="6" style="letter-spacing: 10px;">
            <button type="button" id="btnVerificar" class="btn btn-primary w-100 fw-bold rounded-pill">Confirmar Código</button>
            <button type="button" class="btn btn-link text-danger mt-2 text-decoration-none small" data-bs-dismiss="modal">Cancelar Registro</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inicializamos los elementos UNA VEZ que la página y Bootstrap ya cargaron
        const formRegistro = document.getElementById('formRegistro');
        const modalVerificacion = new bootstrap.Modal(document.getElementById('modalVerificacion'));
        
        // Cuando presionan "Registrarme"
        formRegistro.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Enviando código...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('<?= base_url('auth/pre_registro') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.close();
                    modalVerificacion.show(); 
                } else {
                    Swal.fire('Error', data.msg, 'error'); 
                }
            })
            .catch(error => {
                Swal.fire('Error de red', 'Ocurrió un problema, intenta de nuevo.', 'error');
            });
        });

        // Cuando presionan "Confirmar Código" en el Modal
        document.getElementById('btnVerificar').addEventListener('click', function() {
            const codigo = document.getElementById('codigoVerificacion').value;
            
            if(codigo.length !== 6) {
                Swal.fire('Aviso', 'El código debe tener 6 dígitos', 'warning');
                return;
            }

            // Loader para la confirmación
            Swal.fire({
                title: 'Verificando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append('codigo', codigo);

            fetch('<?= base_url('auth/verificar_codigo') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    modalVerificacion.hide();
                    Swal.fire({
                        title: '¡Cuenta Creada!',
                        text: 'Bienvenido a NewPhoneMX. Ya puedes iniciar sesión.',
                        icon: 'success',
                        confirmButtonText: 'Ir a Iniciar Sesión'
                    }).then(() => {
                        window.location.href = '<?= base_url('login') ?>';
                    });
                } else {
                    Swal.fire('Código Incorrecto', data.msg, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo verificar el código.', 'error');
            });
        });
    </script>
  </body>
</html>