<?php
class IndexController extends Controllers
{
    // Carga el Index_model automáticamente gracias a loadClassmodels() en Controllers.php
    // $this->model estará disponible.

    public function __construct() {
        parent::__construct();
    }

    // Muestra la vista de inicio (Login)
    public function Index()
    {
        // Si el usuario ya está autenticado, redirige a la página principal
        if (Session::getSession('User')) {
            header('Location: ' . URL . 'Home'); // Asume que Home es la página principal
            exit;
        }
        
        // Carga la vista de Login (asume que la vista se llama Index/Index.php o Index/Login.php)
        $this->view->render($this, "index"); 
    }

    // Lógica para iniciar sesión
    public function Login()
    {
        // Si no es POST, redirige al index.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Modelo de datos para preservar el input (email) y errores
        $model1 = (object)['Email' => $email, 'Password' => $password];
        $model2 = (object)['Email' => '', 'Password' => '', 'Role' => ''];
        $hasError = false;

        // 1. VALIDACIÓN BÁSICA
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $model2->Email = "Ingrese un correo electrónico válido.";
            $hasError = true;
        }
        if (empty($password)) {
            $model2->Password = "La contraseña es requerida.";
            $hasError = true;
        }

        if ($hasError) {
            Session::setSession('model1', $model1);
            Session::setSession('model2', $model2);
            header('Location: ' . URL);
            exit;
        }

        // 2. BÚSQUEDA Y VERIFICACIÓN DE CREDENCIALES
        $user = $this->model->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Error genérico por seguridad (no decir si el email o la contraseña falló)
            $model2->Role = "Credenciales incorrectas o el usuario no existe.";
            Session::setSession('model1', $model1);
            Session::setSession('model2', $model2);
            header('Location: ' . URL);
            exit;
        }

        // 3. INICIO DE SESIÓN EXITOSO
        // Limpiar modelos y establecer la sesión del usuario
        Session::setSession('model1', null);
        Session::setSession('model2', null);
        
        // Almacena solo la info esencial y segura en sesión
        Session::setSession('User', [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email']
        ]);

        header('Location: ' . URL . 'Main/Main'); // Redirige a la página principal
        exit;
    }
    
    // Muestra la vista de registro
    public function Register()
    {
        $this->view->render($this, "register");
    }

    // Lógica para registrar un nuevo usuario
    public function CreateUser()
    {
        // Si no es POST, redirige al formulario de registro.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'Index/Register');
            exit;
        }
        
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Modelos para preservar el input y errores (debes crear la vista register
        // para manejar estos modelos)
        $model1 = (object)['FullName' => $fullName, 'Email' => $email];
        $model2 = (object)['FullName' => '', 'Email' => '', 'Password' => '', 'ConfirmPassword' => ''];
        $hasError = false;

        // 1. VALIDACIÓN DE REGISTRO
        if (empty($fullName)) {
            $model2->FullName = "El nombre completo es obligatorio.";
            $hasError = true;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $model2->Email = "Ingrese un correo electrónico válido.";
            $hasError = true;
        }
        if ($this->model->emailExists($email)) {
             $model2->Email = "Este correo ya está registrado.";
             $hasError = true;
        }
        if (strlen($password) < 8) {
            $model2->Password = "La contraseña debe tener al menos 8 caracteres.";
            $hasError = true;
        }
        if ($password !== $confirmPassword) {
            $model2->ConfirmPassword = "Las contraseñas no coinciden.";
            $hasError = true;
        }
        
        if ($hasError) {
            Session::setSession('model1', $model1);
            Session::setSession('model2', $model2);
            $this->view->render($this, "register");
            exit;
        }
        
        // 2. INSERCIÓN SEGURA (Hashing)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $success = $this->model->registerUser($fullName, $email, $password_hash);
        
        if ($success) {
            // Redirige al login con un mensaje de éxito (usando una sesión temporal si tu sistema lo permite)
            Session::setSession('alert_message', [
                'type' => 'success',
                'title' => '¡Registro Exitoso! 🎉',
                'text' => 'Tu cuenta ha sido creada. Ya puedes iniciar sesión.'
            ]);
            header('Location: ' . URL); // Redirige al Login (Index)
            exit;
        } else {
            // Manejar error de DB
           Session::setSession('alert_message', [
                'type' => 'error',
                'title' => 'Error de Servidor',
                'text' => 'Ocurrió un error al registrar el usuario. Inténtalo de nuevo.'
            ]);
            // Si falla, es mejor redirigir al registro de nuevo
            header('Location: ' . URL . 'Index/Register'); 
            exit;
        }
    }
    
    public function Logout()
    {
        Session::destroy(); // Destruye toda la sesión (más seguro que limpiar claves una a una)
        header('Location: '.URL);
        exit;
    }
}
?>