<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['rol', 'nombre', 'apellidos', 'correo', 'password', 'telefono', 'activo','intentos_fallidos', 'bloqueado', 'codigo_recuperacion'];

    // =================================================================
    // TRAER SOLO LOS USUARIOS ACTIVOS
    // =================================================================
    public function obtenerActivos()
    {
        return $this->where('activo', 1)->findAll();
    }

    // =================================================================
    // FUNCIÓN PARA GUARDAR UN NUEVO USUARIO 
    // =================================================================
    public function guardarUsuarioSeguro($datosPost)
    {
        // Encriptar la contraseña 
        $passwordHash = password_hash($datosPost['password'], PASSWORD_DEFAULT);

        // paquete de datos
        $datosNuevos = [
            'nombre'    => $datosPost['nombre'],
            'apellidos' => $datosPost['apellidos'],
            'correo'    => $datosPost['correo'],
            'telefono'  => $datosPost['telefono'],
            'rol'       => $datosPost['rol'],
            'password'  => $passwordHash,
            'activo'    => 1  
        ];

        return $this->insert($datosNuevos);
    }

    // =================================================================
    // REACTIVAR USUARIO
    // =================================================================
    public function reactivar($id)
    {
        return $this->update($id, ['activo' => 1]);
    }

    // =================================================================
    // ELIMINACIÓN (DESACTIVAR)
    // =================================================================
    public function bajaLogica($id)
    {
        return $this->update($id, ['activo' => 0]);
    }
}