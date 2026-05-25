<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->has('id')) {
            return redirect()->to('/login')->with('alerta_intruso', 'Debes iniciar sesión primero.');
        }

        if ($session->get('rol') !== 'admin') {
            return redirect()->to('/dashboard/cliente')->with('alerta_intruso', 'Acceso denegado. Área exclusiva de administración.');
        }

        $tiempo_maximo = 900; 

        if ($session->has('ultima_actividad')) {
            $tiempo_inactivo = time() - $session->get('ultima_actividad');

            if ($tiempo_inactivo > $tiempo_maximo) {
                $session->remove(['id', 'rol', 'ultima_actividad']);
                
                return redirect()->to('/login')->with('alerta_intruso', 'Tu sesión ha expirado por inactividad. Por tu seguridad, vuelve a ingresar.');
            }
        }

        $session->set('ultima_actividad', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada aquí
    }
}