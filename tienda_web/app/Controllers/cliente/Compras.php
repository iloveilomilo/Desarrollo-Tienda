<?php

namespace App\Controllers\cliente;

use App\Controllers\BaseController;

class Compras extends BaseController
{
    public function index()
    {
        $db         = \Config\Database::connect();
        $usuario_id = session('id');

        $pedidos = $db->table('pedidos p')
            ->select('p.*, d.calle, d.numero_exterior, d.ciudad')
            ->join('direcciones_usuarios d', 'd.id = p.direccion_envio_id')
            ->where('p.cliente_id', $usuario_id)
            ->orderBy('p.fecha', 'DESC')
            ->get()->getResultArray();

        foreach ($pedidos as &$p) {
            $p['productos'] = $db->table('detalles_pedido dp')
                ->select('dp.*, pr.nombre as producto_nombre, pr.marca')
                ->join('inventario i', 'i.id = dp.inventario_id')
                ->join('productos pr', 'pr.id = i.producto_id')
                ->where('dp.pedido_id', $p['id'])
                ->get()->getResultArray();
        }

        return view('cliente/mis_compras', ['pedidos' => $pedidos]);
    }
}
