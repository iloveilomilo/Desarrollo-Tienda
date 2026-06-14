<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;

class Perfil extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $usuario_id = session('id');
        
        $usuario = $db->table('usuarios')->where('id', $usuario_id)->get()->getRowArray();
        
        return view('Administrador/perfil', ['usuario' => $usuario]);
    }

    public function actualizar_datos()
    {
        $db = \Config\Database::connect();
        $usuario_id = session('id');

        // VALIDACIÓN DEL CORREO
        $reglas = [
            'correo' => [
                'rules'  => "required|valid_email|is_unique[usuarios.correo,id,{$usuario_id}]",
                'errors' => [
                    'is_unique' => 'El correo electrónico que intentas usar ya está registrado por otra cuenta.'
                ]
            ]
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->with('error', $this->validator->getError('correo'));
        }

        // SI PASA LA VALIDACIÓN, ACTUALIZAMOS
        $data = [
            'nombre'    => $this->request->getPost('nombre'),
            'apellidos' => $this->request->getPost('apellidos'),
            'correo'    => $this->request->getPost('correo'),
            'telefono'  => $this->request->getPost('telefono')
        ];

        $foto = $this->request->getFile('foto_perfil');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $nombreFoto = $foto->getRandomName();
            
            $foto->move(FCPATH . 'uploads/perfiles', $nombreFoto);
            
            $data['foto_perfil'] = $nombreFoto; 
            
            session()->set('foto_perfil', $nombreFoto);
        }

        $db->table('usuarios')->where('id', $usuario_id)->update($data);
        session()->set('nombre', $data['nombre']);

        return redirect()->back()->with('mensaje', 'Tus datos y foto fueron actualizados correctamente.');
    }
}