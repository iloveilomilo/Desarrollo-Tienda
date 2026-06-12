<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;

class Categorias extends BaseController
{
    public function index()
    {
        $model = new CategoriaModel();
        
        $data = [
            'categorias' => $model->findAll() 
        ];

        return view('Administrador/categorias', $data);
    }

    public function store()
    {
        $model = new CategoriaModel();
        
        if (!$this->validate(['nombre' => 'required|min_length[3]'])) {
            return redirect()->back()->with('msg', 'El nombre es muy corto.');
        }

        $model->save([
            'nombre' => $this->request->getPost('nombre'),
            'activo' => 1  
        ]);

        return redirect()->to('/admin/categorias')->with('msg', 'Categoría creada con éxito');
    }

    public function delete($id)
    {
        $model = new CategoriaModel();
        
        // Desactivacion 
        $model->bajaLogica($id);
        
        return redirect()->to('/admin/categorias')->with('msg', 'Categoría desactivada correctamente.');
    }

    public function reactivar($id)
    {
        $model = new CategoriaModel();
        
        $model->reactivarCategoria($id);
        
        return redirect()->to('/admin/categorias')->with('msg', 'Categoría reactivada con éxito.');
    }
}