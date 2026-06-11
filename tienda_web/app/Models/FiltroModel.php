<?php

namespace App\Models;

use CodeIgniter\Model;

class FiltroModel extends Model
{
    protected $table            = 'filtros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'activo'];

    // =================================================================
    // TRAER SOLO LOS FILTROS ACTIVOS
    // =================================================================
    public function obtenerActivos()
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

    // =================================================================
    // REACTIVACIÓN (ALTA LÓGICA)
    // =================================================================
    public function reactivarFiltro($id)
    {
        return $this->update($id, ['activo' => 1]);
    }
}