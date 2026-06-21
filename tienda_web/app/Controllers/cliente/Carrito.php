<?php

namespace App\Controllers\cliente;

use App\Controllers\BaseController;
use App\Models\CarritoModel;

class Carrito extends BaseController
{
    public function index()
    {
        $carritoModel = new CarritoModel();
        $usuario_id = session('id');

        $items = $carritoModel->obtenerCarrito($usuario_id);
        $carrito = [];
        $total = 0;

        foreach ($items as $item) {
            $precioFinal = $item['precio'];
            if ($item['descuento'] > 0) {
                $precioFinal = $item['precio'] - ($item['precio'] * ($item['descuento'] / 100));
            }

            $carrito[] = [
                'id'            => $item['carrito_id'],
                'inventario_id' => $item['inventario_id'],
                'nombre'        => $item['nombre'],
                'imagen'        => $item['imagen'],
                'precio'        => $precioFinal,
                'cantidad'      => $item['cantidad']
            ];

            $total += ($precioFinal * $item['cantidad']);
        }

        return view('cliente/carrito', ['carrito' => $carrito, 'total' => $total]);
    }

    public function agregar()
    {
        $carritoModel  = new CarritoModel();
        $usuario_id    = session('id');
        $inventario_id = $this->request->getPost('id');

        $db      = \Config\Database::connect();
        $producto = $db->table('inventario')->select('stock')->where('id', $inventario_id)->get()->getRowArray();

        if (!$producto || $producto['stock'] <= 0) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Lo sentimos, este producto está agotado.']);
        }

        $existe = $carritoModel->where('usuario_id', $usuario_id)->where('inventario_id', $inventario_id)->first();

        if ($existe) {
            if ($existe['cantidad'] >= $producto['stock']) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'No puedes agregar más, solo hay ' . $producto['stock'] . ' disponibles.']);
            }
            $carritoModel->update($existe['id'], ['cantidad' => $existe['cantidad'] + 1]);
        } else {
            $carritoModel->insert(['usuario_id' => $usuario_id, 'inventario_id' => $inventario_id, 'cantidad' => 1]);
        }

        return $this->response->setJSON(['success' => true, 'mensaje' => 'Producto agregado a tu carrito']);
    }

    public function eliminar($id)
    {
        $carritoModel = new CarritoModel();
        $carritoModel->delete($id);
        return redirect()->to(base_url('carrito'));
    }

    public function actualizar()
    {
        $id     = $this->request->getPost('id');
        $accion = $this->request->getPost('accion');

        $carritoModel = new CarritoModel();
        $item = $carritoModel->find($id);

        if ($item) {
            $nuevaCantidad = $item['cantidad'];

            if ($accion == 'plus') {
                $db      = \Config\Database::connect();
                $producto = $db->table('inventario')->select('stock')->where('id', $item['inventario_id'])->get()->getRowArray();
                if ($nuevaCantidad >= $producto['stock']) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Límite de stock alcanzado (' . $producto['stock'] . ')']);
                }
                $nuevaCantidad++;
            } elseif ($accion == 'minus' && $nuevaCantidad > 1) {
                $nuevaCantidad--;
            }

            $carritoModel->update($id, ['cantidad' => $nuevaCantidad]);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar']);
    }
}
