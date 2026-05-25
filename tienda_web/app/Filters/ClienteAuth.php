<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ClienteAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('id')) {
            return redirect()->to('/login');
        }

        if (session()->get('rol') !== 'cliente') {
            return redirect()->to('/login'); 
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}