<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function admin()
    {
        // En el Sprint 5 se conectará el modelo de KPIs
        // Por ahora retornamos la vista del panel con datos vacíos
        return view('Administrador/admin');
    }
}
