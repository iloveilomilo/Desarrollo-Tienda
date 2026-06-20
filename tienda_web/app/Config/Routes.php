<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =================================================================
// Rutas Públicas — Catálogo visible para todos (HU-05)
// =================================================================
$routes->get('/', 'Administrador\Dashboard::cliente');
$routes->get('dashboard/cliente', 'Administrador\Dashboard::cliente');
$routes->get('tienda/producto/(:num)', 'Administrador\Dashboard::detalle/$1');

// =================================================================
// Rutas de Autenticación y Registro
// =================================================================
$routes->get('/login', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Registro de clientes con verificación por correo
$routes->get('/registro', 'Auth::registro');
$routes->post('/auth/pre_registro', 'Auth::pre_registro');
$routes->post('/auth/verificar_codigo', 'Auth::verificar_codigo');

// Validación del token JWT enviado por correo (2FA)
$routes->get('auth/validar_token/(:any)', 'Auth::validar_token/$1');

// Recuperación de contraseña
$routes->post('auth/solicitar_recuperacion', 'Auth::solicitar_recuperacion');
$routes->post('auth/restablecer_password', 'Auth::restablecer_password');

// =================================================================
// Rutas del Panel de Administración (protegidas por filtro adminAuth)
// =================================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Administrador', 'filter' => 'adminAuth'], function($routes) {

    $routes->get('panel', 'Dashboard::admin');

    // Categorías
    $routes->get('categorias', 'Categorias::index');
    $routes->post('categorias/guardar', 'Categorias::store');
    $routes->get('categorias/eliminar/(:num)', 'Categorias::delete/$1');
    $routes->get('categorias/reactivar/(:num)', 'Categorias::reactivar/$1');

    // Filtros dinámicos
    $routes->get('filtros', 'Filtros::index');
    $routes->post('filtros/guardar', 'Filtros::store');
    $routes->get('filtros/eliminar/(:num)', 'Filtros::delete/$1');
    $routes->get('filtros/reactivar/(:num)', 'Filtros::reactivar/$1');

    // Usuarios internos
    $routes->get('usuarios', 'Usuarios::index');
    $routes->post('usuarios/guardar', 'Usuarios::store');
    $routes->get('usuarios/eliminar/(:num)', 'Usuarios::delete/$1');
    $routes->get('usuarios/reactivar/(:num)', 'Usuarios::reactivar/$1');

    // Productos (CRUD + baja lógica)
    $routes->get('productos', 'Productos::index');
    $routes->get('productos/crear', 'Productos::create');
    $routes->post('productos/guardar', 'Productos::store');
    $routes->get('productos/editar/(:num)', 'Productos::edit/$1');
    $routes->post('productos/actualizar/(:num)', 'Productos::actualizar/$1');
    $routes->get('productos/eliminar/(:num)', 'Productos::delete/$1');

    // Perfil del administrador
    $routes->get('perfil', 'Perfil::index');
    $routes->post('perfil/actualizar', 'Perfil::actualizar_datos');

    // Soporte interno (admin atiende a agentes de Atención al Cliente)
    $routes->get('soporte', 'SoporteAdmin::index');
    $routes->get('soporte/chat/(:num)', 'SoporteAdmin::ver_chat/$1');
    $routes->post('soporte/responder', 'SoporteAdmin::responder');
        
    $routes->get('carrito',                 'cliente\Carrito::index',       ['filter' => 'clienteAuth']);
    $routes->post('carrito/agregar',        'cliente\Carrito::agregar',     ['filter' => 'clienteAuth']);
    $routes->get('carrito/eliminar/(:num)', 'cliente\Carrito::eliminar/$1', ['filter' => 'clienteAuth']);
    $routes->post('carrito/actualizar',     'cliente\Carrito::actualizar',  ['filter' => 'clienteAuth']);
    // Carrito de Compras
    $routes->get('carrito',                   'cliente\Carrito::index',           ['filter' => 'clienteAuth']);
    $routes->post('carrito/agregar',          'cliente\Carrito::agregar',         ['filter' => 'clienteAuth']);
    $routes->get('carrito/eliminar/(:num)',   'cliente\Carrito::eliminar/$1',     ['filter' => 'clienteAuth']);
    $routes->post('carrito/actualizar',       'cliente\Carrito::actualizar',      ['filter' => 'clienteAuth']);

    // Soporte al Cliente
    $routes->post('soporte/enviar_duda',              'cliente\SoporteCliente::enviar_duda',    ['filter' => 'clienteAuth']);
    $routes->get('mis-preguntas',                     'cliente\SoporteCliente::mis_preguntas', ['filter' => 'clienteAuth']);
    $routes->get('mis-preguntas/chat/(:num)',          'cliente\SoporteCliente::ver_chat/$1',   ['filter' => 'clienteAuth']);
    $routes->post('mis-preguntas/responder',           'cliente\SoporteCliente::responder_chat',['filter' => 'clienteAuth']);

    // Perfil y Direcciones
    $routes->get('perfil',                            'cliente\Perfil::index',               ['filter' => 'clienteAuth']);
    $routes->post('perfil/actualizar_datos',          'cliente\Perfil::actualizar_datos',    ['filter' => 'clienteAuth']);
    $routes->post('perfil/guardar_direccion',         'cliente\Perfil::guardar_direccion',   ['filter' => 'clienteAuth']);
    $routes->get('perfil/eliminar_direccion/(:num)',  'cliente\Perfil::eliminar_direccion/$1',['filter' => 'clienteAuth']);

    // Checkout y Pago (Mercado Pago)
    $routes->get('checkout',                          'cliente\Checkout::index',    ['filter' => 'clienteAuth']);
    $routes->post('checkout/procesar',                'cliente\Checkout::procesar', ['filter' => 'clienteAuth']);
    $routes->get('checkout/exito',                    'cliente\Checkout::exito',    ['filter' => 'clienteAuth']);

    // Mis Compras
    $routes->get('mis-compras',                       'cliente\Compras::index',     ['filter' => 'clienteAuth']);
});


// Carrito de Compras
$routes->get('carrito',                   'cliente\Carrito::index',           ['filter' => 'clienteAuth']);
$routes->post('carrito/agregar',          'cliente\Carrito::agregar',         ['filter' => 'clienteAuth']);
$routes->get('carrito/eliminar/(:num)',   'cliente\Carrito::eliminar/$1',     ['filter' => 'clienteAuth']);
$routes->post('carrito/actualizar',       'cliente\Carrito::actualizar',      ['filter' => 'clienteAuth']);

// Soporte al Cliente
$routes->post('soporte/enviar_duda',              'cliente\SoporteCliente::enviar_duda',    ['filter' => 'clienteAuth']);
$routes->get('mis-preguntas',                     'cliente\SoporteCliente::mis_preguntas', ['filter' => 'clienteAuth']);
$routes->get('mis-preguntas/chat/(:num)',          'cliente\SoporteCliente::ver_chat/$1',   ['filter' => 'clienteAuth']);
$routes->post('mis-preguntas/responder',           'cliente\SoporteCliente::responder_chat',['filter' => 'clienteAuth']);

// Perfil y Direcciones
$routes->get('perfil',                            'cliente\Perfil::index',               ['filter' => 'clienteAuth']);
$routes->post('perfil/actualizar_datos',          'cliente\Perfil::actualizar_datos',    ['filter' => 'clienteAuth']);
$routes->post('perfil/guardar_direccion',         'cliente\Perfil::guardar_direccion',   ['filter' => 'clienteAuth']);
$routes->get('perfil/eliminar_direccion/(:num)',  'cliente\Perfil::eliminar_direccion/$1',['filter' => 'clienteAuth']);

// Checkout y Pago (Mercado Pago)
$routes->get('checkout',                          'cliente\Checkout::index',    ['filter' => 'clienteAuth']);
$routes->post('checkout/procesar',                'cliente\Checkout::procesar', ['filter' => 'clienteAuth']);
$routes->get('checkout/exito',                    'cliente\Checkout::exito',    ['filter' => 'clienteAuth']);

// Mis Compras
$routes->get('mis-compras',                       'cliente\Compras::index',     ['filter' => 'clienteAuth']);

// ============================================================
// Módulo Atención al Cliente 
// ============================================================

$routes->group('soporte', ['namespace' => 'App\Controllers\Soporte', 'filter' => 'soporteAuth'], function ($routes) {
    $routes->get('panel',                    'Soporte::index');
    $routes->get('mensajes',                 'Soporte::mensajes');
    $routes->get('historial',                'Soporte::historial');
    $routes->get('responder/(:num)',         'Soporte::responder/$1');
    $routes->post('enviar_mensaje',          'Soporte::enviar_mensaje');
    $routes->post('actualizar_estado',       'Soporte::actualizar_estado');
    $routes->get('cerrar/(:num)',            'Soporte::cerrar_conversacion/$1');
    $routes->get('mensajes_ajax/(:num)',     'Soporte::obtener_mensajes_nuevos/$1');
});