<?php
// api/index.php - Router principal para Vercel
// Todas las peticiones pasan por aquí

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$query = parse_url($requestUri, PHP_URL_QUERY);

// Directorio raíz del proyecto (un nivel arriba de api/)
$root = __DIR__ . '/..';

// Archivos PHP que existen en la raíz
$phpFiles = [
    '/index.php',
    '/admin.php',
    '/rsvp_handler.php',
];

// Determinar qué archivo PHP ejecutar
$fileToLoad = null;

if ($path === '/' || $path === '' || $path === '/index.php') {
    $fileToLoad = $root . '/index.php';
} elseif ($path === '/admin.php') {
    $fileToLoad = $root . '/admin.php';
} elseif ($path === '/rsvp_handler.php') {
    $fileToLoad = $root . '/rsvp_handler.php';
}

if ($fileToLoad && file_exists($fileToLoad)) {
    require $fileToLoad;
    exit;
}

// Si no coincide ninguna ruta, devolver 404
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title></head><body><h1>404 - No encontrado</h1></body></html>';
