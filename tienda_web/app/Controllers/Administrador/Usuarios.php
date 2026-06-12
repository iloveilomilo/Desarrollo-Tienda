<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    public function index()
    {
        $model = new UsuarioModel();
        
        $data = [
            // Se restringe la consulta para obtener exclusivamente al personal del sistema
            'usuarios' => $model->whereIn('rol', ['admin', 'atencion_cliente'])->findAll()
        ];

        return view('Administrador/usuarios', $data);
    }

    public function store()
    {
        //  reglas de seguridad del formulario (correo unico en la bd, telefono de 10 dígitos y rol admin o atencion_cliente)
        $reglas = [
            'nombre'    => 'required|min_length[3]',
            'apellidos' => 'required|min_length[3]',
            'correo'    => 'required|valid_email|is_unique[usuarios.correo]',
            'password'  => 'required|min_length[6]',
            'telefono'  => 'required|exact_length[10]|numeric',
            'rol'       => 'required|in_list[admin,atencion_cliente]'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->with('msg', 'Error: Verifica que el correo no esté repetido, que el teléfono sea de 10 números y que llenes todos los campos.');
        }

        $model = new UsuarioModel();
        $model->guardarUsuarioSeguro($this->request->getPost());

        return redirect()->to('/admin/usuarios')->with('msg', 'Usuario registrado con éxito.');
    }

    public function delete($id)
    {
        if ($id == session('id')) {
            return redirect()->to('/admin/usuarios')->with('msg', '¡Acción denegada! No puedes desactivar tu propia cuenta por seguridad.');
        }

        $model = new UsuarioModel();
        $model->bajaLogica($id);
        
        return redirect()->to('/admin/usuarios')->with('msg', 'El acceso del usuario ha sido revocado (Desactivado).');
    }

    public function reactivar($id)
    {
        $model = new UsuarioModel();
        
        $model->reactivar($id);
        
        return redirect()->to('/admin/usuarios')->with('msg', 'El acceso del usuario ha sido reactivado con éxito.');
    }
}