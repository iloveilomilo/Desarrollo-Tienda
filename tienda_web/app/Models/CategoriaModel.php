<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'activo'];

    // =================================================================
    // TRAER SOLO LAS CATEGORÍAS ACTIVAS
    // =================================================================
    public function obtenerActivas()
    {
        return $this->where('activo', 1)->findAll();
    }

    // =================================================================
    // ELIMINACIÓN (DESACTIVAR)
    // =================================================================
    public function bajaLogica($id)
    {
        return $this->update($id, ['activo' => 0]);
    }

    // ==========================================
    // REACTIVACIÓN
    // ==========================================
    public function reactivarCategoria($id)
    {
        return $this->update($id, ['activo' => 1]);
    }
}