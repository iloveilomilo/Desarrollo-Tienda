<?php

namespace App\Controllers\cliente;

use App\Controllers\BaseController;
use App\Models\CarritoModel;

class Checkout extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $usuario_id = session('id');

        $carritoModel = new CarritoModel();
        $items = $carritoModel->obtenerCarrito($usuario_id);

        if (empty($items)) {
            return redirect()->to(base_url('carrito'))->with('mensaje', 'Tu carrito está vacío.');
        }

        $total = 0;
        foreach ($items as &$item) {
            $precioFinal = $item['precio'];
            if ($item['descuento'] > 0) {
                $precioFinal = $item['precio'] - ($item['precio'] * ($item['descuento'] / 100));
            }
            $item['precio_final'] = $precioFinal;
            $total += ($precioFinal * $item['cantidad']);
        }

        $direcciones = $db->table('direcciones_usuarios')->where('usuario_id', $usuario_id)->get()->getResultArray();

        return view('cliente/checkout', [
            'carrito'    => $items,
            'total'      => $total,
            'direcciones'=> $direcciones
        ]);
    }

    public function procesar()
    {
        $db = \Config\Database::connect();
        $usuario_id   = session('id');
        $direccion_id = $this->request->getPost('direccion_id');

        if (!$direccion_id) {
            return redirect()->back()->with('mensaje', 'Debes seleccionar una dirección de envío.');
        }

        session()->set('direccion_envio_id', $direccion_id);

        $carritoModel = new CarritoModel();
        $itemsCarrito = $carritoModel->obtenerCarrito($usuario_id);

        $itemsMercadoPago = [];
        foreach ($itemsCarrito as $item) {
            $precioFinal = $item['precio'];
            if ($item['descuento'] > 0) {
                $precioFinal = $item['precio'] - ($item['precio'] * ($item['descuento'] / 100));
            }

            $itemsMercadoPago[] = [
                'title'       => $item['nombre'],
                'description' => 'Celular de Tienda NEWPHONEMX',
                'quantity'    => (int) $item['cantidad'],
                'currency_id' => 'MXN',
                'unit_price'  => (float) number_format($precioFinal, 2, '.', '')
            ];
        }

        $token  = env('MP_ACCESS_TOKEN');
        $client = \Config\Services::curlrequest();

        $body = [
            'items'              => $itemsMercadoPago,
            'back_urls'          => [
                'success' => base_url('checkout/exito'),
                'failure' => base_url('carrito'),
                'pending' => base_url('carrito')
            ],
            'external_reference' => uniqid('ORDEN_')
        ];

        try {
            $response = $client->post('https://api.mercadopago.com/checkout/preferences', [
                'headers'    => [
                    'Authorization' => 'Bearer ' . trim($token),
                    'Content-Type'  => 'application/json'
                ],
                'json'       => $body,
                'verify'     => false,
                'http_errors'=> false
            ]);

            $bodyResponse = json_decode($response->getBody());

            if (isset($bodyResponse->init_point)) {
                return redirect()->to($bodyResponse->init_point);
            }

            $errorDetalle = isset($bodyResponse->message) ? $bodyResponse->message : json_encode($bodyResponse);
            return redirect()->back()->with('mensaje', 'Error de Mercado Pago: ' . $errorDetalle);

        } catch (\Exception $e) {
            return redirect()->back()->with('mensaje', 'Error de conexión: ' . $e->getMessage());
        }
    }

    public function exito()
    {
        $db           = \Config\Database::connect();
        $usuario_id   = session('id');
        $direccion_id = session('direccion_envio_id');

        $payment_id = $this->request->getGet('payment_id')
                   ?? $this->request->getGet('preference_id')
                   ?? $this->request->getGet('collection_id')
                   ?? 'MP-' . strtoupper(substr(md5(time()), 0, 10));

        if (!$direccion_id) {
            return redirect()->to(base_url('carrito'));
        }

        $carritoModel = new CarritoModel();
        $items = $carritoModel->obtenerCarrito($usuario_id);

        if (empty($items)) {
            return redirect()->to(base_url('dashboard/cliente'));
        }

        $usuario    = $db->table('usuarios')->where('id', $usuario_id)->get()->getRowArray();
        $total      = 0;
        $filasTabla = '';

        foreach ($items as $item) {
            $precioFinal = $item['precio'];
            if ($item['descuento'] > 0) {
                $precioFinal = $item['precio'] - ($item['precio'] * ($item['descuento'] / 100));
            }
            $total      += ($precioFinal * $item['cantidad']);
            $filasTabla .= "<tr>
                <td style='padding:10px;border-bottom:1px solid #eee;'>{$item['nombre']} x {$item['cantidad']}</td>
                <td style='padding:10px;border-bottom:1px solid #eee;text-align:right;'>$" . number_format($precioFinal * $item['cantidad'], 2) . "</td>
            </tr>";
        }

        // Registrar pedido
        $db->table('pedidos')->insert([
            'cliente_id'         => $usuario_id,
            'direccion_envio_id' => $direccion_id,
            'total'              => $total,
            'estado'             => 'pagado',
            'fecha'              => date('Y-m-d H:i:s')
        ]);
        $pedido_id = $db->insertID();

        $detalles = [];
        foreach ($items as $item) {
            $db->table('inventario')->where('id', $item['inventario_id'])
               ->set('stock', 'stock - ' . $item['cantidad'], false)->update();

            $precioFinal = $item['precio'] - ($item['precio'] * ($item['descuento'] / 100));
            $detalles[] = [
                'pedido_id'       => $pedido_id,
                'inventario_id'   => $item['inventario_id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $precioFinal
            ];
        }
        $db->table('detalles_pedido')->insertBatch($detalles);
        $db->table('carrito')->where('usuario_id', $usuario_id)->delete();
        session()->remove('direccion_envio_id');

        // Envío de correo de confirmación
        $config = [
            'protocol'   => getenv('email.protocol'),
            'SMTPHost'   => getenv('email.SMTPHost'),
            'SMTPUser'   => getenv('email.SMTPUser'),
            'SMTPPass'   => getenv('email.SMTPPass'),
            'SMTPPort'   => (int) getenv('email.SMTPPort'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            'mailType'   => getenv('email.mailType'),
            'charset'    => getenv('email.charset'),
            'CRLF'       => "\r\n",
            'newline'    => "\r\n"
        ];

        $email = \Config\Services::email();
        $email->initialize($config);
        $email->setFrom(getenv('email.SMTPUser'), 'NewPhoneMX Store');
        $email->setTo($usuario['correo']);
        $email->setSubject('✅ Confirmación de Compra #' . $pedido_id . ' - NewPhoneMX');
        $email->setMessage("
        <div style='background-color:#f4f7f6;padding:30px;font-family:Segoe UI,sans-serif;'>
            <div style='max-width:600px;margin:auto;background:#fff;border-radius:15px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.1);'>
                <div style='background:#007bff;padding:40px;text-align:center;color:white;'>
                    <h1 style='margin:0;font-size:28px;'>¡Pago Confirmado!</h1>
                    <p style='opacity:0.9;'>Gracias por confiar en NewPhoneMX</p>
                </div>
                <div style='padding:30px;'>
                    <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
                    <p>Tu orden ha sido recibida con éxito.</p>
                    <div style='background:#f8f9fa;padding:20px;border-radius:10px;margin-bottom:25px;'>
                        <p style='margin:5px 0;'><strong>ID Pedido:</strong> #{$pedido_id}</p>
                        <p style='margin:5px 0;'><strong>Transacción MP:</strong> <span style='color:#007bff;'>{$payment_id}</span></p>
                        <p style='margin:5px 0;'><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</p>
                    </div>
                    <table style='width:100%;border-collapse:collapse;'>
                        <thead><tr>
                            <th style='text-align:left;padding:10px;border-bottom:2px solid #eee;'>Producto</th>
                            <th style='text-align:right;padding:10px;border-bottom:2px solid #eee;'>Subtotal</th>
                        </tr></thead>
                        <tbody>{$filasTabla}</tbody>
                        <tfoot><tr>
                            <td style='padding:20px 10px;font-size:18px;'><strong>Total Pagado</strong></td>
                            <td style='padding:20px 10px;font-size:22px;color:#28a745;text-align:right;'><strong>$" . number_format($total, 2) . "</strong></td>
                        </tr></tfoot>
                    </table>
                </div>
                <div style='background:#333;color:white;padding:20px;text-align:center;font-size:12px;'>
                    © 2026 NewPhoneMX Store. Todos los derechos reservados.
                </div>
            </div>
        </div>");
        $email->send();

        return view('cliente/exito', ['pedido_id' => $pedido_id, 'transaccion' => $payment_id]);
    }
}
