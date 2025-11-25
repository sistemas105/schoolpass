<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir la ruta base del proyecto
define('BASE_PATH', dirname(__DIR__));

// PASO 1: Cargar archivos
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';

// PASO 2: Inicializar la base de datos y el controlador de autenticación
try {
    $db = (new Database())->getConnection();
    // Creamos la instancia del controlador, pasando la conexión de la DB
    $auth = new AuthController($db); 

} catch (\Exception $e) {
    // Si la conexión falla, detenemos la ejecución y mostramos el error
    die('ERROR Fatal de Conexión a la Base de Datos: ' . $e->getMessage());
}

// PASO 3: Lógica del Router
$method = $_SERVER['REQUEST_METHOD'];

// Solución definitiva para la URI:
// 1. Usar REQUEST_URI para obtener la ruta completa (/auth/register?foo=bar)
$uri = $_SERVER['REQUEST_URI'] ?? '';

// 2. Eliminar la query string (lo que viene después de ?)
$uri = strtok($uri, '?'); 

// 3. Eliminar el subdirectorio si existe. (Si el proyecto no está en la raíz, 
//    reemplaza '/SchoolPass' por el nombre de tu carpeta si es necesario)
//    Asumimos que el proyecto está en la raíz del dominio, así que solo limpiamos index.php.
$basePathToRemove = '/index.php'; // Lo que el servidor de IONOS está agregando
if (strpos($uri, $basePathToRemove) === 0) {
    $uri = substr($uri, strlen($basePathToRemove));
}
// También limpiamos el path si la aplicación estuviera en /SchoolPass/
// $uri = str_replace('/SchoolPass', '', $uri);

// 4. Quitar barras iniciales/finales (Convierte '/auth/register' en 'auth/register')
$uri = trim($uri, '/');


// *** DEBUGGING: Mostrar el valor de $uri y detener la ejecución ***
// die("DEBUG: La URI detectada es: '" . $uri . "'"); // Dejamos esta línea comentada

switch (true) {
    // Ruta principal: index.php o ruta vacía
    case $uri === '' && $method === 'GET':
        echo '<h1>SchoolPass</h1><p>OK router. Prueba <a href="/auth/register">/auth/register</a></p>';
        break;

    // Mostrar formulario de login
    case $uri === 'auth/login' && $method === 'GET':
        // DEBUGGING TEMPORAL: Mostrar la URI que llega a esta ruta
        die("DEBUG LOGIN URI: '" . $uri . "'"); 
        // $auth->login(); // Línea original
        break;

    // Mostrar formulario de registro
    case $uri === 'auth/register' && $method === 'GET':
        $auth->register();
        break;

    // Procesar formulario de registro (POST)
    case $uri === 'auth/register' && $method === 'POST':
        // RESTAURADA la función original
        $auth->storeRegister(); 
        break;

    // 404 Not Found 
    default:
        http_response_code(404);
        echo "Not Found"; 
        break;
}