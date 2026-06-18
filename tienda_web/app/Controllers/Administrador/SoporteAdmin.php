<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\ChatModel;

class SoporteAdmin extends BaseController
{
    public function index()
    {
        $chatModel = new ChatModel();
        $mi_id = session('id');

        $data['chats'] = $chatModel->obtenerMisChatsInternos($mi_id);

        return view('Administrador/soporte/index', $data);
    }

    public function ver_chat($sala_id)
    {
        $chatModel = new ChatModel();

        $sala = $chatModel->find($sala_id);
        if (!$sala) {
            return redirect()->to('/admin/soporte')->with('msg', 'La sala de chat no existe.');
        }

        $mi_id = session('id');
        $data['chats']       = $chatModel->obtenerMisChatsInternos($mi_id);
        $data['sala_actual'] = $sala;
        $data['mensajes']    = $chatModel->obtenerMensajesDeSala($sala_id);

        return view('Administrador/soporte/index', $data);
    }

    public function responder()
    {
        $sala_id = $this->request->getPost('sala_chat_id');
        $mensaje = $this->request->getPost('mensaje');
        $mi_id   = session('id');

        if (empty(trim($mensaje))) {
            return redirect()->back()->with('msg', 'No puedes enviar un mensaje vacío.');
        }

        $chatModel = new ChatModel();
        $chatModel->guardarMensaje([
            'sala_chat_id' => $sala_id,
            'remitente_id' => $mi_id,
            'mensaje'      => $mensaje,
            'fecha_envio'  => date('Y-m-d H:i:s')
        ]);

        $chatModel->update($sala_id, ['estado' => 'espera_cliente']);

        return redirect()->to('/admin/soporte/chat/' . $sala_id);
    }
}
