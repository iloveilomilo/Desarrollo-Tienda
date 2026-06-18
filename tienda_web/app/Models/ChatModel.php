<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table      = 'salas_chat';
    protected $primaryKey = 'id';
    protected $allowedFields = ['cliente_id', 'soporte_id', 'estado', 'fecha_inicio', 'fecha_cierre'];

    public function obtenerMisChatsInternos($admin_id)
    {
        return $this->select('salas_chat.*, usuarios.nombre as empleado_nombre, usuarios.apellidos as empleado_apellidos')
                    ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
                    ->where('usuarios.rol', 'atencion_cliente')
                    ->where('salas_chat.soporte_id', $admin_id)
                    ->orderBy('salas_chat.fecha_inicio', 'DESC')
                    ->findAll();
    }

    public function obtenerMensajesDeSala($sala_id)
    {
        return $this->db->table('mensajes_chat')
                    ->select('mensajes_chat.*, usuarios.nombre as remitente')
                    ->join('usuarios', 'usuarios.id = mensajes_chat.remitente_id')
                    ->where('sala_chat_id', $sala_id)
                    ->orderBy('fecha_envio', 'ASC')
                    ->get()
                    ->getResultArray();
    }

    public function guardarMensaje($datosMensaje)
    {
        return $this->db->table('mensajes_chat')->insert($datosMensaje);
    }
}
