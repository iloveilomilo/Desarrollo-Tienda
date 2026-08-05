<?php

namespace App\Controllers\Soporte;

use App\Controllers\BaseController;
use App\Models\ChatModel;

class ChatAdmin extends BaseController
{
    public function index()
    {
        $chatModel = new ChatModel();
        $mi_id = session('id');

        $data['admins'] = $chatModel->obtenerAdminsConEstado($mi_id);

        return view('AtencionCliente/chat_admins', $data);
    }

    public function ver_chat($admin_id)
    {
        $db = \Config\Database::connect();
        $admin = $db->table('usuarios')
            ->select('id, nombre, apellidos')
            ->where('id', $admin_id)
            ->where('rol', 'admin')
            ->where('activo', 1)
            ->get()->getRowArray();

        if (!$admin) {
            return redirect()->to('/soporte/admins')->with('msg', 'Ese administrador no existe o ya no está activo.');
        }

        $chatModel = new ChatModel();
        $mi_id = session('id');

        $sala = $chatModel->obtenerSalaConAdmin($mi_id, $admin_id);

        $data['admins']       = $chatModel->obtenerAdminsConEstado($mi_id);
        $data['admin_actual'] = $admin;
        $data['sala_actual']  = $sala;
        $data['mensajes']     = $sala ? $chatModel->obtenerMensajesDeSala($sala['id']) : [];

        return view('AtencionCliente/chat_admins', $data);
    }

    public function responder()
    {
        $admin_id = $this->request->getPost('admin_id');
        $mensaje  = $this->request->getPost('mensaje');
        $mi_id    = session('id');

        if (empty(trim($mensaje))) {
            return redirect()->back()->with('msg', 'No puedes enviar un mensaje vacío.');
        }

        $chatModel = new ChatModel();

        // Si nunca le has escrito a este admin, se crea la sala aquí mismo
        $sala = $chatModel->obtenerSalaConAdmin($mi_id, $admin_id);

        if ($sala) {
            $sala_id = $sala['id'];
            $chatModel->update($sala_id, ['estado' => 'nuevo']);
        } else {
            $sala_id = $chatModel->insert([
                'cliente_id'   => $mi_id,
                'soporte_id'   => $admin_id,
                'estado'       => 'nuevo',
                'fecha_inicio' => date('Y-m-d H:i:s')
            ]);
        }

        $chatModel->guardarMensaje([
            'sala_chat_id' => $sala_id,
            'remitente_id' => $mi_id,
            'mensaje'      => $mensaje,
            'fecha_envio'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/soporte/admins/chat/' . $admin_id);
    }
}
