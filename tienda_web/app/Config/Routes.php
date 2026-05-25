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
