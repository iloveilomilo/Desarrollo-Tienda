<?php
header('Content-Type: text/plain');

echo "=== DEBUG INFO ===\n\n";

$htaccess = __DIR__ . '/.htaccess';
echo ".htaccess existe: " . (file_exists($htaccess) ? 'SI' : 'NO') . "\n";
if (file_exists($htaccess)) {
    echo "Tamano: " . filesize($htaccess) . " bytes\n";
    echo "Primera linea: " . trim(explode("\n", file_get_contents($htaccess))[0]) . "\n";
}

echo "\n";

if (function_exists('apache_get_modules')) {
    $mods = apache_get_modules();
    echo "mod_rewrite cargado: " . (in_array('mod_rewrite', $mods) ? 'SI' : 'NO') . "\n";
    echo "Modulos: " . implode(', ', $mods) . "\n";
} else {
    echo "apache_get_modules() NO disponible\n";
}

echo "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
