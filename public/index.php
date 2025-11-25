<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';

$db = (new Database())->getConnection();
$auth = new AuthController($db);

// Detectar URI correctamente en IONOS
$uri = $_SERVER['REDIRECT_URL'] ?? $_SERVER['PATH_INFO'] ?? '';
$uri = trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {
    case $uri === '' && $method === 'GET':
        echo '<h1>SchoolPass</h1><p>OK router. Prueba <a href="/auth/register">/auth/register</a></p>';
        break;

    case $uri === 'auth/login' && $method === 'GET':
        $auth->login();
        break;

    case $uri === 'auth/register' && $method === 'GET':
        $auth->register();
        break;

    case $uri === 'auth/register' && $method === 'POST':
        $auth->storeRegister();
        break;

    default:
        http_response_code(404);
       Not Found";
        break;
}
