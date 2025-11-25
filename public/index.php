<?php
// Mostrar errores en desarrollo (puedes desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir base del proyecto (un nivel arriba de /public)
define('BASE_PATH', dirname(__DIR__));

// Cargar dependencias con rutas absolutas
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';

// Crear conexión PDO y controlador
$db = (new Database())->getConnection();
$auth = new AuthController($db);

// Normalizar la URI


$uri = $_SERVER['PATH_INFO'] ?? '';
$uri = trim($uri, '/'); // e.g. "auth/register"
$method = $_SERVER['REQUEST_METHOD'];


echo "<pre>URI: " . $uri . "</pre>";
// Router
switch (true) {
    // Página principal (opcional)
    case $uri === '' && $method === 'GET':
        echo '<h1>SchoolPass</h1><p>OK router. Prueba <a href="/auth/register">/auth/register</a></p>';
        break;

    // Auth
    case $uri === 'auth/login' && $method === 'GET':
        $auth->login();
        break;

    case $uri === 'auth/register' && $method === 'GET':
        $auth->register();
        break;

    case $uri === 'auth/register' && $method === 'POST':
        $auth->storeRegister();
        break;

    // 404
    default:
        // ⚠️ Importante: no prints antes de los headers
        http_response_code(404);
        echo "404 Not Found";
        break;
}

