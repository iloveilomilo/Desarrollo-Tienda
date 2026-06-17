<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\ProductoClienteModel;

class Dashboard extends BaseController
{
    public function admin()
    {
        // En el Sprint 5 se conectará el modelo de KPIs
        return view('Administrador/admin');
    }

    public function cliente()
    {
        $productoModel = new ProductoClienteModel();
        $request = service('request');

        $busqueda = $request->getGet('q');
        $filtros = [
            'categoria'  => $request->getGet('categoria'),
            'marca'      => $request->getGet('marca'),
            'condicion'  => $request->getGet('condicion'),
            'precio_min' => $request->getGet('precio_min'),
            'precio_max' => $request->getGet('precio_max'),
        ];

        $data = [
            'productos'  => $productoModel->getProductosDisponibles($busqueda, $filtros),
            'marcas'     => $productoModel->getMarcasDisponibles(),
            'categorias' => $productoModel->getCategoriasDisponibles(),
            'busqueda'   => $busqueda,
            'filtros'    => $filtros
        ];

        return view('cliente/inicio', $data);
    }

    public function detalle($id)
    {
        $db = \Config\Database::connect();

        $producto = $db->table('productos')
            ->select('productos.*, inventario.id as inventario_id, inventario.precio, inventario.stock, inventario.condicion, inventario.descuento, inventario.sku, inventario.caja_original, inventario.cable_cargador, inventario.esim')
            ->join('inventario', 'inventario.producto_id = productos.id')
            ->where('productos.id', $id)
            ->where('inventario.activo', 1)
            ->get()->getRowArray();

        if (!$producto) {
            return redirect()->to('/')->with('msg', 'Producto no encontrado.');
        }

        $imagenes = $db->table('imagenes_producto')->where('producto_id', $id)->get()->getResultArray();
        $filtros  = $db->table('valores_filtros')
            ->select('filtros.nombre, valores_filtros.valor')
            ->join('filtros', 'filtros.id = valores_filtros.filtro_id')
            ->where('valores_filtros.inventario_id', $producto['inventario_id'])
            ->get()->getResultArray();

        return view('cliente/inicio', [
            'producto' => $producto,
            'imagenes' => $imagenes,
            'filtros'  => $filtros
        ]);
    }
}
