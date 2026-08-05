<?php
header('Content-Type: text/plain');

$logDir = __DIR__ . '/../writable/logs/';
$files = glob($logDir . '*.log');

if (empty($files)) {
    echo "No hay archivos de log todavia.\n";
    exit;
}

usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
$latest = $files[0];

echo "=== " . basename($latest) . " (ultimas 200 lineas) ===\n\n";

$lines = file($latest);
$lines = array_slice($lines, -200);
echo implode('', $lines);
