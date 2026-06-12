<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table            = 'productos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['categoria_id', 'nombre', 'marca', 'descripcion', 'imagen_principal', 'fecha_eliminacion'];

    // =================================================================
    // INVENTARIO COMPLETO
    // =================================================================
    public function obtenerInventarioCompleto()
    {
        return $this->select('
                productos.id, 
                productos.nombre, 
                productos.imagen_principal, 
                inventario.sku, 
                inventario.condicion, 
                inventario.precio, 
                inventario.descuento, 
                inventario.stock
            ')
            ->join('inventario', 'inventario.producto_id = productos.id', 'inner')
            ->where('inventario.activo', 1)
            ->orderBy('productos.id', 'DESC')
            ->findAll(); 
    }


    // =================================================================
    // GUARDAR PRODUCTO, INVENTARIO Y FILTROS  
    // =================================================================
    public function guardarProductoCompleto($datosProducto, $postData)
    {
        try {
            $this->db->transException(true)->transStart();

            // Insertar en productos
            $this->db->table('productos')->insert($datosProducto);
            $productoId = $this->db->insertID();

            // Generación de SKU Secuencial
            $marcaStr = strtoupper(substr($datosProducto['marca'], 0, 3));
            $condicionStr = strtoupper(substr($postData['condicion'], 0, 1));
            
            $ultimoRegistro = $this->db->table('inventario')->selectMax('id')->get()->getRow();
            $siguienteNumero = ($ultimoRegistro && $ultimoRegistro->id) ? $ultimoRegistro->id + 1 : 1;
            
            $consecutivo = str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);
            $skuGenerado = 'NP-' . $marcaStr . '-' . $condicionStr . '-' . $consecutivo;

            // Insertar en Inventario
            $datosInventario = [
                'producto_id'    => $productoId,
                'sku'            => $skuGenerado,
                'condicion'      => $postData['condicion'],
                'caja_original'  => isset($postData['caja_original']) ? 1 : 0,
                'cable_cargador' => isset($postData['cable_cargador']) ? 1 : 0,
                'esim'           => isset($postData['esim']) ? 1 : 0,
                'precio'         => $postData['precio'],
                'stock'          => $postData['stock'],
                'descuento'      => $postData['descuento'] ?? 0,
                'activo'         => 1
            ];
            $this->db->table('inventario')->insert($datosInventario);
            $inventarioId = $this->db->insertID();

            // Insertar Filtros 
            $filtros = $this->db->table('filtros')->get()->getResultArray();
            foreach ($filtros as $filtro) {
                // Se busca si el valor viene en el POST
                $valor = $postData['filtro_' . $filtro['id']] ?? null;
                if (!empty($valor)) {
                    $this->db->table('valores_filtros')->insert([
                        'inventario_id' => $inventarioId,
                        'filtro_id'     => $filtro['id'],
                        'valor'         => $valor
                    ]);
                }
            }

            $this->db->transComplete();

            return $skuGenerado;

        } catch (\Throwable $e) {
            // Si algo falla, deshacemos los cambios en la BD y lanzamos el error hacia el controlador
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            throw $e; 
        }
    }

    // =================================================================
    // ELIMINACIÓN (DESACTIVACIÓN)
    // =================================================================
    public function bajaLogica($id)
    {
        $this->db->transStart();
        
        // Desactivar el producto principal
        $this->update($id, ['fecha_eliminacion' => date('Y-m-d H:i:s')]);
        
        // Desactivar el inventario relacionado 
        $this->db->table('inventario')->where('producto_id', $id)->update(['activo' => 0]);
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }
    


    // =================================================================
    // OBTENER UN PRODUCTO COMPLETO PARA EDITAR
    // =================================================================
    public function obtenerProductoCompletoPorId($id)
    {
        return $this->select('productos.*, inventario.id as inventario_id, inventario.sku, inventario.condicion, inventario.caja_original, inventario.cable_cargador, inventario.esim, inventario.precio, inventario.stock, inventario.descuento')
                    ->join('inventario', 'inventario.producto_id = productos.id', 'inner')
                    ->where('productos.id', $id)
                    ->first();
    }

    // =================================================================
    // ACTUALIZAR PRODUCTO, INVENTARIO Y FILTROS
    // =================================================================
    public function actualizarProductoCompleto($id, $datosProducto, $postData)
    {
        try {
            $this->db->transException(true)->transStart();

            // 1. Actualizar tabla productos (Datos generales)
            $this->update($id, $datosProducto);

            // 2. Actualizar tabla inventario
            $datosInventario = [
                'condicion'      => $postData['condicion'],
                'caja_original'  => isset($postData['caja_original']) ? 1 : 0,
                'cable_cargador' => isset($postData['cable_cargador']) ? 1 : 0,
                'esim'           => isset($postData['esim']) ? 1 : 0,
                'precio'         => $postData['precio'],
                'stock'          => $postData['stock'],
                'descuento'      => $postData['descuento'] ?? 0
            ];
            $this->db->table('inventario')->where('producto_id', $id)->update($datosInventario);

            // Necesitamos el ID del inventario para los filtros
            $inventario = $this->db->table('inventario')->where('producto_id', $id)->get()->getRow();
            $inventarioId = $inventario->id;

            // 3. Actualizar Filtros (La forma más limpia es borrar los viejos y crear los nuevos)
            $this->db->table('valores_filtros')->where('inventario_id', $inventarioId)->delete();
            
            $filtros = $this->db->table('filtros')->get()->getResultArray();
            foreach ($filtros as $filtro) {
                $valor = $postData['filtro_' . $filtro['id']] ?? null;
                if (!empty($valor)) {
                    $this->db->table('valores_filtros')->insert([
                        'inventario_id' => $inventarioId,
                        'filtro_id'     => $filtro['id'],
                        'valor'         => $valor
                    ]);
                }
            }

            $this->db->transComplete();
            return true;

        } catch (\Throwable $e) {
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            throw $e; 
        }
    }

}