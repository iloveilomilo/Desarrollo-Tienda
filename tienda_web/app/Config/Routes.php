<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Página de inicio (temporal hasta implementar catálogo)
$routes->get('/', 'Home::index');

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
});
