<?php

namespace App\Controllers\cliente;

use App\Controllers\BaseController;

class Perfil extends BaseController
{
    public function index()
    {
        $db         = \Config\Database::connect();
        $usuario_id = session('id');

        $usuario     = $db->table('usuarios')->where('id', $usuario_id)->get()->getRowArray();
        $direcciones = $db->table('direcciones_usuarios')->where('usuario_id', $usuario_id)->get()->getResultArray();

        return view('cliente/perfil', ['usuario' => $usuario, 'direcciones' => $direcciones]);
    }

    public function actualizar_datos()
    {
        $db         = \Config\Database::connect();
        $usuario_id = session('id');

        $reglas = [
            'correo' => [
                'rules'  => "required|valid_email|is_unique[usuarios.correo,id,{$usuario_id}]",
                'errors' => [
                    'is_unique' => 'El correo electrónico que intentas usar ya está registrado por otra cuenta.'
                ]
            ]
        ];

        if (!$this->validate($reglas)) {
            return redirect()->to(base_url('perfil'))->with('error', $this->validator->getError('correo'));
        }

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

        return redirect()->to(base_url('perfil'))->with('mensaje', 'Tus datos personales y foto fueron actualizados.');
    }

    public function guardar_direccion()
    {
        $db = \Config\Database::connect();

        $db->table('direcciones_usuarios')->insert([
            'usuario_id'      => session('id'),
            'nombre_recibe'   => $this->request->getPost('nombre_recibe'),
            'telefono_recibe' => $this->request->getPost('telefono_recibe'),
            'calle'           => $this->request->getPost('calle'),
            'numero_exterior' => $this->request->getPost('numero_exterior'),
            'numero_interior' => $this->request->getPost('numero_interior'),
            'colonia'         => $this->request->getPost('colonia'),
            'codigo_postal'   => $this->request->getPost('codigo_postal'),
            'ciudad'          => $this->request->getPost('ciudad'),
            'estado'          => $this->request->getPost('estado'),
            'referencia'      => $this->request->getPost('referencia')
        ]);

        return redirect()->to(base_url('perfil'))->with('mensaje', 'Dirección guardada correctamente.');
    }

    public function eliminar_direccion($id)
    {
        $db = \Config\Database::connect();
        $db->table('direcciones_usuarios')->where('id', $id)->where('usuario_id', session('id'))->delete();
        return redirect()->to(base_url('perfil'));
    }
}
