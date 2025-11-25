<?php
class Router {
    public function dispatch($uri) {
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path == '/' || $path == '/login') {
            require '../app/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->login();
        } elseif ($path == '/register') {
            require '../app/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->register();
        } elseif ($path == '/dashboard') {
            require '../app/controllers/DashboardController.php';
            $controller = new DashboardController();
            $controller->index();
        } else {
            echo "404 - Ruta no encontrada";
        }
    }
}