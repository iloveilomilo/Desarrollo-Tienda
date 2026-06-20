<?php

namespace App\Controllers\Soporte;

use App\Controllers\BaseController;

class Soporte extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $nuevos = $db->table('salas_chat')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
            ->where('usuarios.rol', 'cliente')
            ->where('salas_chat.estado', 'nuevo')
            ->countAllResults();

        $en_proceso = $db->table('salas_chat')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
            ->where('usuarios.rol', 'cliente')
            ->whereIn('salas_chat.estado', ['en_proceso', 'espera_cliente'])
            ->countAllResults();

        $resueltos = $db->table('salas_chat')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
            ->where('usuarios.rol', 'cliente')
            ->where('salas_chat.estado', 'cerrado')
            ->countAllResults();

        return view('AtencionCliente/index', compact('nuevos', 'en_proceso', 'resueltos'));
    }

    public function mensajes()
    {
        $db = \Config\Database::connect();

        $conversaciones = $db->table('salas_chat')
            ->select('salas_chat.*, usuarios.nombre, usuarios.apellidos')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id', 'left')
            ->where('usuarios.rol', 'cliente')
            ->where('salas_chat.estado !=', 'cerrado')
            ->orderBy('salas_chat.fecha_inicio', 'DESC')
            ->get()->getResultArray();

        return view('AtencionCliente/mensajes', ['conversaciones' => $conversaciones]);
    }

    public function historial()
    {
        $db = \Config\Database::connect();

        $conversaciones = $db->table('salas_chat')
            ->select('salas_chat.*, usuarios.nombre, usuarios.apellidos')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id', 'left')
            ->where('usuarios.rol', 'cliente')
            ->where('salas_chat.estado', 'cerrado')
            ->orderBy('salas_chat.fecha_inicio', 'DESC')
            ->get()->getResultArray();

        return view('AtencionCliente/historial', ['conversaciones' => $conversaciones]);
    }

    public function responder($id)
    {
        $db = \Config\Database::connect();

        $conversacion = $db->table('salas_chat')
            ->select('salas_chat.*, usuarios.nombre, usuarios.apellidos')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
            ->where('salas_chat.id', $id)
            ->get()->getRowArray();

        if (!$conversacion) {
            return redirect()->to(base_url('soporte/mensajes'))->with('msg', 'Conversación no encontrada.');
        }

        // Al abrir un ticket nuevo, el agente lo toma → en_proceso
        if ($conversacion['estado'] === 'nuevo') {
            $db->table('salas_chat')->where('id', $id)->update(['estado' => 'en_proceso']);
            $conversacion['estado'] = 'en_proceso';
        }

        return view('AtencionCliente/responder', ['conversacion' => $conversacion]);
    }

    public function enviar_mensaje()
    {
        $db      = \Config\Database::connect();
        $id_sala = $this->request->getPost('id_conversacion');
        $mensaje = trim($this->request->getPost('mensaje'));
        $mi_id   = session('id');

        if (!empty($mensaje)) {
            $db->table('mensajes_chat')->insert([
                'sala_chat_id' => $id_sala,
                'remitente_id' => $mi_id,
                'mensaje'      => $mensaje,
                'fecha_envio'  => date('Y-m-d H:i:s')
            ]);
            // Agente respondió → esperando respuesta del cliente
            $db->table('salas_chat')->where('id', $id_sala)->update(['estado' => 'espera_cliente']);
        }

        return redirect()->to(base_url('soporte/responder/' . $id_sala));
    }

    public function actualizar_estado()
    {
        $db      = \Config\Database::connect();
        $id_sala = $this->request->getPost('id_conversacion');
        $estado  = $this->request->getPost('estado');

        $estados_validos = ['nuevo', 'en_proceso', 'espera_cliente'];
        if (in_array($estado, $estados_validos)) {
            $db->table('salas_chat')->where('id', $id_sala)->update(['estado' => $estado]);
        }

        return redirect()->to(base_url('soporte/responder/' . $id_sala))->with('msg', 'Estado actualizado.');
    }

    public function cerrar_conversacion($id)
    {
        $db = \Config\Database::connect();
        $db->table('salas_chat')->where('id', $id)->update([
            'estado'       => 'cerrado',
            'fecha_cierre' => date('Y-m-d H:i:s')
        ]);
        return redirect()->to(base_url('soporte/historial'))->with('msg', 'Conversación cerrada y archivada.');
    }

    public function obtener_mensajes_nuevos($id_sala)
    {
        $db = \Config\Database::connect();

        $mensajes = $db->table('mensajes_chat')
            ->select('mensajes_chat.*, usuarios.nombre, usuarios.rol')
            ->join('usuarios', 'usuarios.id = mensajes_chat.remitente_id')
            ->where('sala_chat_id', $id_sala)
            ->orderBy('fecha_envio', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['mensajes' => $mensajes]);
    }
}
