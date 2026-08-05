<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends BaseController
{
    public function index()
    {
        // Si ya tiene ID en sesión, lo mandamos a su panel segun su rol
        if (session()->has('id')) {
            return $this->redirigirPorRol(session()->get('rol'));
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UsuarioModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $data = $model->where('correo', $email)->first();

        if ($data) {
            
            // Bloquear acceso a usuarios dados de baja
            if ($data['activo'] == 0) {
                log_message('warning', 'Intento de acceso denegado (Cuenta Inactiva). Correo: ' . $email);
                $session->setFlashdata('msg', 'Tu cuenta ha sido desactivada. Contacta a soporte.');
                return redirect()->to('/login');
            }

            // Verificar si la cuenta está bloqueada por intentos fallidos
            if (isset($data['bloqueado']) && $data['bloqueado'] == 1) {
                log_message('warning', 'Intento de acceso a cuenta BLOQUEADA. Correo: ' . $email);
                $session->setFlashdata('msg', 'Tu cuenta ha sido bloqueada tras 3 intentos fallidos.');
                return redirect()->to('/login');
            }

            $pass = $data['password'];
            $auth = false;

            // Verificación SEGURA (Hash)
            if (password_verify($password, $pass)) {
                $auth = true;
            } 
            elseif ($password === $pass) {
                $auth = true;
                $model->update($data['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
            }

            if ($auth) {
                // Si entró correctamente, reseteamos los intentos fallidos a 0
                if (isset($data['intentos_fallidos']) && $data['intentos_fallidos'] > 0) {
                    $model->update($data['id'], ['intentos_fallidos' => 0]);
                }

                // ==========================================
                // MAGIA JWT: Crear Token de 5 Minutos
                // ==========================================
                $llave_secreta = getenv('JWT_SECRET');
                $payload = [
                    'iat' => time(), 
                    'exp' => time() + (5 * 60), 
                    'id_usuario' => $data['id']
                ];

                $token_acceso = JWT::encode($payload, $llave_secreta, 'HS256');

                // ==========================================
                // ENVIAR EL CORREO AL USUARIO 
                // ==========================================
                $email_service = \Config\Services::email();

                $config = [
                    'protocol'   => getenv('email.protocol'),
                    'SMTPHost'   => getenv('email.SMTPHost'),
                    'SMTPUser'   => getenv('email.SMTPUser'),
                    'SMTPPass'   => getenv('email.SMTPPass'),
                    'SMTPPort'   => (int) getenv('email.SMTPPort'),
                    'SMTPCrypto' => getenv('email.SMTPCrypto'),
                    'mailType'   => getenv('email.mailType'),
                    'charset'    => getenv('email.charset'),
                    'CRLF'       => "\r\n", 
                    'newline'    => "\r\n"  
                ];
                
                $email_service->initialize($config);

                // Definimos quién lo envía y a quién va
                $email_service->setFrom(getenv('email.SMTPUser'), 'NewPhoneMX Seguridad');
                $email_service->setTo($data['correo']);
                $email_service->setSubject('Acceso Seguro a tu Cuenta - NewPhoneMX');
                
                $enlace = base_url('auth/validar_token/' . $token_acceso);
                
                $html = "<h2>Hola, " . $data['nombre'] . "</h2>";
                $html .= "<p>Has solicitado iniciar sesión. Haz clic en el botón de abajo para entrar de forma segura a tu cuenta.</p>";
                $html .= "<p><b>⚠️ Este enlace caduca en exactamente 5 minutos por tu seguridad.</b></p>";
                $html .= "<a href='{$enlace}' style='display:inline-block; padding:10px 20px; background-color:#6f42c1; color:#ffffff; text-decoration:none; border-radius:5px; font-weight:bold;'>Ingresar a mi cuenta</a>";
                $html .= "<br><br><p><small>Si no solicitaste este acceso, puedes ignorar este correo.</small></p>";
                
                $email_service->setMessage($html);
                
                if ($email_service->send()) {
                    log_message('info', 'Token JWT de acceso enviado por correo al usuario ID: ' . $data['id']);
                    $session->setFlashdata('success', 'Te hemos enviado un enlace de seguridad a tu correo. Tienes 5 minutos para confirmar tu acceso.');
                } else {
                    $session->setFlashdata('msg', 'Hubo un problema al enviar el correo. Por favor, intenta de nuevo o contacta a soporte.');
                }
                
                return redirect()->to('/login');
            } else {
                // Lógica cuando la contraseña es incorrecta
                $intentosActuales = isset($data['intentos_fallidos']) ? $data['intentos_fallidos'] : 0;
                $nuevosIntentos = $intentosActuales + 1;
                
                $datosUpdate = ['intentos_fallidos' => $nuevosIntentos];

                if ($nuevosIntentos >= 3) {
                    // Bloqueamos la cuenta
                    $datosUpdate['bloqueado'] = 1;
                    $model->update($data['id'], $datosUpdate);
                    log_message('critical', 'CUENTA BLOQUEADA por múltiples intentos fallidos. Correo: ' . $email);
                    $session->setFlashdata('msg', 'Contraseña incorrecta. Has alcanzado el límite de 3 intentos y tu cuenta ha sido bloqueada. Usa "Recuperar Contraseña".');
                } else {
                    // Restamos y mostramos cuántos quedan
                    $model->update($data['id'], $datosUpdate);
                    $intentosRestantes = 3 - $nuevosIntentos;
                    log_message('notice', 'Contraseña incorrecta. Correo: ' . $email . '. Intentos restantes: ' . $intentosRestantes);
                    $session->setFlashdata('msg', "Contraseña incorrecta. Te quedan {$intentosRestantes} intento(s) antes de bloquear la cuenta.");
                }
                

                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Correo no encontrado');
            log_message('notice', 'Intento de login con correo inexistente: ' . $email);
            return redirect()->to('/login');
        }
    }

    // =========================================================
    // VALIDAR EL CLIC DESDE EL CORREO
    // =========================================================
    public function validar_token($token)
    {
        $session = session();
        $llave_secreta = getenv('JWT_SECRET');

        try {
            $decodificado = JWT::decode($token, new Key($llave_secreta, 'HS256'));
            
            // Si llegamos aquí, el token es completamente válido y está en tiempo.
            $id_usuario = $decodificado->id_usuario;

            $model = new UsuarioModel();
            $data = $model->find($id_usuario);

            if (!$data || $data['activo'] == 0) {
                $session->setFlashdata('msg', 'Usuario no encontrado o inactivo.');
                return redirect()->to('/login');
            }

            // Crear el Token de Sesión de 1 DÍA (Mantiene la sesión viva por 24 horas)
            $payload_sesion = [
                'iat' => time(),
                'exp' => time() + (24 * 60 * 60), // 24 horas * 60 minutos * 60 segundos
                'id_usuario' => $data['id']
            ];
            $token_sesion = JWT::encode($payload_sesion, $llave_secreta, 'HS256');

            // Iniciar sesión finalmente
            $ses_data = [
                'id'           => $data['id'],
                'nombre'       => $data['nombre'],
                'email'        => $data['correo'],
                'rol'          => $data['rol'],
                'foto_perfil'  => $data['foto_perfil'] ?? null,
                'token_sesion' => $token_sesion, 
                'is_logged_in' => true
            ];
            $session->set($ses_data);

            return $this->redirigirPorRol($data['rol']);

        } catch (\Exception $e) {
            // El token expiró o es inválido
            log_message('error', 'Fallo de seguridad: Intento de uso de JWT inválido o expirado. Error: ' . $e->getMessage());
            $session->setFlashdata('msg', 'El enlace de seguridad ha caducado (pasaron más de 5 minutos) o es inválido. Vuelve a iniciar sesión.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    // Función auxiliar para redirigir según rol
    private function redirigirPorRol($rol) {
        switch($rol) {
            case 'admin': return redirect()->to('/admin/panel');
            case 'atencion_cliente': return redirect()->to('soporte/soporte');
            default: return redirect()->to('dashboard/cliente');
        }
    }

    // =========================================================
    // FUNCIONES DE REGISTRO DE NUEVOS CLIENTES
    // =========================================================

    public function registro()
    {
        // Muestra la vista del formulario
        return view('auth/registro');
    }

    // =========================================================
    // 1. RECIBIR DATOS, CREAR CÓDIGO Y ENVIAR CORREO
    // =========================================================
    public function pre_registro()
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        $correo = $this->request->getPost('correo');

        // Validamos que el correo no exista
        if ($usuarioModel->where('correo', $correo)->first()) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Este correo ya está registrado.']);
        }

        // Generamos un código aleatorio de 6 dígitos
        $codigo = rand(100000, 999999);

        // Guardamos todo en la sesión temporal (incluyendo el código)
        $temp_data = [
            'nombre'    => $this->request->getPost('nombre'),
            'apellidos' => $this->request->getPost('apellidos'),
            'correo'    => $correo,
            'telefono'  => $this->request->getPost('telefono'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'       => 'cliente',
            'activo'    => 1,
            'codigo_verificacion' => $codigo
        ];
        session()->set('temp_registro', $temp_data);

        // CONFIGURACIÓN DEL CORREO (Reutilizamos la que ya tienes en tu login)
        $email_service = \Config\Services::email();
        $config = [
            'protocol'   => getenv('email.protocol'),
            'SMTPHost'   => getenv('email.SMTPHost'),
            'SMTPUser'   => getenv('email.SMTPUser'),
            'SMTPPass'   => getenv('email.SMTPPass'),
            'SMTPPort'   => (int) getenv('email.SMTPPort'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            'mailType'   => getenv('email.mailType'),
            'charset'    => getenv('email.charset'),
            'CRLF'       => "\r\n", 
            'newline'    => "\r\n"  
        ];
        $email_service->initialize($config);
        $email_service->setFrom(getenv('email.SMTPUser'), 'NewPhoneMX');
        $email_service->setTo($correo);
        $email_service->setSubject('Tu Código de Verificación');
        
        $html = "<h2>Hola, {$temp_data['nombre']}</h2>";
        $html .= "<p>Estás a un paso de crear tu cuenta en NewPhoneMX.</p>";
        $html .= "<p>Tu código de verificación es: <b style='font-size: 24px; color: #654696;'>{$codigo}</b></p>";
        $email_service->setMessage($html);

        if ($email_service->send()) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'msg' => 'No pudimos enviar el correo. Revisa tu dirección.']);
        }
    }

    // =========================================================
    // 2. VALIDAR EL CÓDIGO Y GUARDAR EN LA BASE DE DATOS
    // =========================================================
    public function verificar_codigo()
    {
        $codigo_ingresado = $this->request->getPost('codigo');
        $temp_data = session()->get('temp_registro');

        if (!$temp_data) {
            return $this->response->setJSON(['success' => false, 'msg' => 'La sesión expiró o fue cancelada. Vuelve a llenar tus datos.']);
        }

        // Si el código es correcto
        if ($codigo_ingresado == $temp_data['codigo_verificacion']) {
            
            // Quitamos el código del arreglo porque esa columna no existe en tu BD
            unset($temp_data['codigo_verificacion']);
            
            // Insertamos al usuario en la BD real
            $usuarioModel = new \App\Models\UsuarioModel();
            $usuarioModel->insert($temp_data);
            
            // Limpiamos la memoria temporal
            session()->remove('temp_registro');
            
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'msg' => 'El código no coincide. Intenta de nuevo.']);
        }
    }

    // FUNCIONES DE RECUPERACIÓN DE CONTRASEÑA

    public function solicitar_recuperacion()
    {
        $model = new UsuarioModel();
        $correo = $this->request->getPost('correo');
        $telefono = $this->request->getPost('telefono');

        // Buscamos si existe alguien con ese correo y teléfono
        $usuario = $model->where('correo', $correo)->where('telefono', $telefono)->first();

        if (!$usuario) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Los datos no coinciden con ninguna cuenta registrada.']);
        }

        $codigo = rand(100000, 999999);
        $model->update($usuario['id'], ['codigo_recuperacion' => $codigo]);

        // Configuramos el correo
        $email_service = \Config\Services::email();
        $config = [
            'protocol'   => getenv('email.protocol'),
            'SMTPHost'   => getenv('email.SMTPHost'),
            'SMTPUser'   => getenv('email.SMTPUser'),
            'SMTPPass'   => getenv('email.SMTPPass'),
            'SMTPPort'   => (int) getenv('email.SMTPPort'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            'mailType'   => getenv('email.mailType'),
            'charset'    => getenv('email.charset'),
            'CRLF'       => "\r\n", 
            'newline'    => "\r\n"  
        ];
        $email_service->initialize($config);

        $email_service->setFrom(getenv('email.SMTPUser'), 'NewPhoneMX Seguridad');
        $email_service->setTo($correo);
        $email_service->setSubject('Código de Recuperación de Cuenta');
        
        $html = "<h2>Hola, " . $usuario['nombre'] . "</h2>";
        $html .= "<p>Hemos recibido una solicitud para recuperar tu cuenta.</p>";
        $html .= "<p>Tu código de seguridad de 6 dígitos es: <b style='font-size:24px; color:#654696;'>{$codigo}</b></p>";
        $html .= "<p><small>Si no solicitaste esto, ignora este mensaje.</small></p>";
        
        $email_service->setMessage($html);

        if ($email_service->send()) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'msg' => 'Hubo un error al enviar el correo con el código.']);
        }
    }

    public function restablecer_password()
    {
        $model = new UsuarioModel();
        $correo = $this->request->getPost('correo');
        $codigo = $this->request->getPost('codigo');
        $nueva_pass = $this->request->getPost('nueva_password');

        // Buscamos al usuario por correo y el código que se le envió
        $usuario = $model->where('correo', $correo)->where('codigo_recuperacion', $codigo)->first();

        if (!$usuario) {
            return $this->response->setJSON(['success' => false, 'msg' => 'El código ingresado es incorrecto.']);
        }

        $datosUpdate = [
            'password' => password_hash($nueva_pass, PASSWORD_DEFAULT),
            'intentos_fallidos' => 0,
            'bloqueado' => 0,
            'codigo_recuperacion' => null 
        ];

        $model->update($usuario['id'], $datosUpdate);

        log_message('info', 'Contraseña restablecida exitosamente para el usuario ID: ' . $usuario['id']);
        
        return $this->response->setJSON(['success' => true, 'msg' => '¡Tu contraseña ha sido restablecida y tu cuenta desbloqueada!']);
    }
}