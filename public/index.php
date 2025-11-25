<?php
require_once '../config/database.php';
require_once '../core/Router.php';

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);