<?php

namespace App\Controllers\Administrador;

use App\Controllers\BaseController;
use App\Models\ProductoClienteModel;

class Dashboard extends BaseController
{
    public function admin()
    {
        $db = \Config\Database::connect();

        $ingresos = $db->table('pedidos')->selectSum('total')->where('estado !=', 'cancelado')->get()->getRow()->total ?? 0;
        $ordenes_pendientes = $db->table('pedidos')->where('estado', 'pendiente')->countAllResults();
        $productos_activos  = $db->table('inventario')->where('activo', 1)->countAllResults();
        $clientes_total     = $db->table('usuarios')->where('rol', 'cliente')->where('activo', 1)->countAllResults();
        $chats_pendientes   = $db->table('salas_chat')
            ->join('usuarios', 'usuarios.id = salas_chat.cliente_id')
            ->where('usuarios.rol', 'atencion_cliente')
            ->whereIn('salas_chat.estado', ['nuevo', 'en_proceso'])
            ->countAllResults();

        $ordenes_recientes = $db->table('pedidos')
            ->select('pedidos.id, pedidos.total, pedidos.estado, pedidos.fecha, usuarios.nombre, usuarios.apellidos')
            ->join('usuarios', 'usuarios.id = pedidos.cliente_id')
            ->orderBy('pedidos.id', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $stock_bajo = $db->table('inventario')
            ->select('productos.nombre, inventario.sku, inventario.stock')
            ->join('productos', 'productos.id = inventario.producto_id')
            ->where('inventario.activo', 1)
            ->where('inventario.stock <=', 3)
            ->orderBy('inventario.stock', 'ASC')
            ->get()->getResultArray();

        return view('Administrador/admin', compact(
            'ingresos', 'ordenes_pendientes', 'productos_activos',
            'clientes_total', 'ordenes_recientes', 'chats_pendientes', 'stock_bajo'
        ));
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
