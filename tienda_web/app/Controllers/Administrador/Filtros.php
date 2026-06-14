<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\FiltroModel;

class Filtros extends BaseController
{
    public function index()
    {
        $model = new FiltroModel();
        
        $data = [
            'filtros' => $model->findAll()
        ];

        return view('Administrador/filtros', $data);
    }

    public function store()
    {
        $model = new FiltroModel();
        
        // Validación
        if (!$this->validate(['nombre' => 'required|min_length[2]'])) {
            return redirect()->back()->with('msg', 'Nombre inválido.');
        }

        $model->save([
            'nombre' => $this->request->getPost('nombre'),
            'activo' => 1  
        ]);

        return redirect()->to('/admin/filtros')->with('msg', 'Filtro creado con éxito');
    }

    public function delete($id)
    {
        $model = new FiltroModel();
        
        $model->bajaLogica($id);
        
        return redirect()->to('/admin/filtros')->with('msg', 'Filtro ocultado correctamente');
    }

    public function reactivar($id)
    {
        $model = new FiltroModel();
        
        $model->reactivarFiltro($id);
        
        return redirect()->to('/admin/filtros')->with('msg', 'Filtro reactivado correctamente');
    }
}