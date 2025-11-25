<?php
require_once '../app/models/User.php';
class AuthController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function login() {
      include BASE_PATH . '/app/views/auth/login.php';
    }

    public function register() {
   include __DIR__ . '/../views/auth/register.php';
    }

    public function storeRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/register'); exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        $errors = [];

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            $errors[] = 'Todos los campos son obligatorios.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Correo inválido.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        $user = new User($this->db);
        if ($user->findByEmail($email)) {
            $errors[] = 'El correo ya está registrado.';
        }

        if (!empty($errors)) {
            $error = implode('<br>', $errors);
            include '../app/views/auth/register.php';
            return;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $user->name = $name;
        $user->email = $email;
        $user->password = $hashed;

        if ($user->create()) {
            $success = 'Usuario registrado correctamente. Inicia sesión.';
            include '../app/views/auth/login.php';
        } else {
            $error = 'Ocurrió un error al registrar.';
            include '../app/views/auth/register.php';
        }
    }
}
