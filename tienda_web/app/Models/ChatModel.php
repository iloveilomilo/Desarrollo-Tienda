<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table      = 'salas_chat';
    protected $primaryKey = 'id';
    protected $allowedFields = ['cliente_id', 'soporte_id', 'estado', 'fecha_inicio', 'fecha_cierre'];

    // Lista a TODOS los agentes de Atención al Cliente activos, junto con
    // el estado de su conversación con este admin (si ya existe alguna).
    public function obtenerAgentesConEstado($admin_id)
    {
        $agentes = $this->db->table('usuarios')
                    ->select('id, nombre, apellidos')
                    ->where('rol', 'atencion_cliente')
                    ->where('activo', 1)
                    ->orderBy('nombre', 'ASC')
                    ->get()
                    ->getResultArray();

        if (empty($agentes)) {
            return [];
        }

        $ids = array_column($agentes, 'id');

        $salas = $this->where('soporte_id', $admin_id)
                    ->whereIn('cliente_id', $ids)
                    ->orderBy('fecha_inicio', 'DESC')
                    ->findAll();

        // Nos quedamos solo con la sala mas reciente por cada agente
        $salaPorAgente = [];
        foreach ($salas as $sala) {
            if (!isset($salaPorAgente[$sala['cliente_id']])) {
                $salaPorAgente[$sala['cliente_id']] = $sala;
            }
        }

        foreach ($agentes as &$agente) {
            $sala = $salaPorAgente[$agente['id']] ?? null;
            $agente['sala_id']      = $sala['id'] ?? null;
            $agente['estado']       = $sala['estado'] ?? null;
            $agente['fecha_inicio'] = $sala['fecha_inicio'] ?? null;
        }

        return $agentes;
    }

    public function obtenerSalaConAgente($admin_id, $agente_id)
    {
        return $this->where('soporte_id', $admin_id)
                    ->where('cliente_id', $agente_id)
                    ->orderBy('fecha_inicio', 'DESC')
                    ->first();
    }

    // Version espejo: lista a TODOS los administradores activos, junto con
    // el estado de la conversación que tiene este agente con cada uno.
    public function obtenerAdminsConEstado($agente_id)
    {
        $admins = $this->db->table('usuarios')
                    ->select('id, nombre, apellidos')
                    ->where('rol', 'admin')
                    ->where('activo', 1)
                    ->orderBy('nombre', 'ASC')
                    ->get()
                    ->getResultArray();

        if (empty($admins)) {
            return [];
        }

        $ids = array_column($admins, 'id');

        $salas = $this->where('cliente_id', $agente_id)
                    ->whereIn('soporte_id', $ids)
                    ->orderBy('fecha_inicio', 'DESC')
                    ->findAll();

        // Nos quedamos solo con la sala mas reciente por cada admin
        $salaPorAdmin = [];
        foreach ($salas as $sala) {
            if (!isset($salaPorAdmin[$sala['soporte_id']])) {
                $salaPorAdmin[$sala['soporte_id']] = $sala;
            }
        }

        foreach ($admins as &$admin) {
            $sala = $salaPorAdmin[$admin['id']] ?? null;
            $admin['sala_id']      = $sala['id'] ?? null;
            $admin['estado']       = $sala['estado'] ?? null;
            $admin['fecha_inicio'] = $sala['fecha_inicio'] ?? null;
        }

        return $admins;
    }

    public function obtenerSalaConAdmin($agente_id, $admin_id)
    {
        return $this->where('cliente_id', $agente_id)
                    ->where('soporte_id', $admin_id)
                    ->orderBy('fecha_inicio', 'DESC')
                    ->first();
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
