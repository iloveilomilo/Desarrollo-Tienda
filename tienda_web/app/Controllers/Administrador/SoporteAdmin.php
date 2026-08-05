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

        $data['agentes'] = $chatModel->obtenerAgentesConEstado($mi_id);

        return view('Administrador/soporte/index', $data);
    }

    public function ver_chat($agente_id)
    {
        $db = \Config\Database::connect();
        $agente = $db->table('usuarios')
            ->select('id, nombre, apellidos')
            ->where('id', $agente_id)
            ->where('rol', 'atencion_cliente')
            ->where('activo', 1)
            ->get()->getRowArray();

        if (!$agente) {
            return redirect()->to('/admin/soporte')->with('msg', 'Ese agente no existe o ya no está activo.');
        }

        $chatModel = new ChatModel();
        $mi_id = session('id');

        $sala = $chatModel->obtenerSalaConAgente($mi_id, $agente_id);

        $data['agentes']       = $chatModel->obtenerAgentesConEstado($mi_id);
        $data['agente_actual'] = $agente;
        $data['sala_actual']   = $sala;
        $data['mensajes']      = $sala ? $chatModel->obtenerMensajesDeSala($sala['id']) : [];

        return view('Administrador/soporte/index', $data);
    }

    public function responder()
    {
        $agente_id = $this->request->getPost('agente_id');
        $mensaje   = $this->request->getPost('mensaje');
        $mi_id     = session('id');

        if (empty(trim($mensaje))) {
            return redirect()->back()->with('msg', 'No puedes enviar un mensaje vacío.');
        }

        $chatModel = new ChatModel();

        // Si el admin nunca le ha escrito a este agente, se crea la sala aquí mismo
        $sala = $chatModel->obtenerSalaConAgente($mi_id, $agente_id);

        if ($sala) {
            $sala_id = $sala['id'];
            $chatModel->update($sala_id, ['estado' => 'espera_cliente']);
        } else {
            $sala_id = $chatModel->insert([
                'cliente_id'   => $agente_id,
                'soporte_id'   => $mi_id,
                'estado'       => 'espera_cliente',
                'fecha_inicio' => date('Y-m-d H:i:s')
            ]);
        }

        $chatModel->guardarMensaje([
            'sala_chat_id' => $sala_id,
            'remitente_id' => $mi_id,
            'mensaje'      => $mensaje,
            'fecha_envio'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin/soporte/chat/' . $agente_id);
    }
}
