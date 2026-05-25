<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SoporteAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('id')) {
            return redirect()->to('/login');
        }

        if (session()->get('rol') !== 'atencion_cliente') {
            // Lo mandamos a su panel de cliente o a una ruta de error
            return redirect()->to('/dashboard/cliente')->with('msg', 'Acceso denegado. Área exclusiva de administración.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}