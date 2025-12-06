<?php
// Extiende de la clase base Controllers para tener acceso a $this->view, loadClassmodels(), etc.
class MainController extends Controllers
{
    // El constructor llama al constructor del padre para inicializar la vista y el modelo (si aplica).
    public function __construct() {
        parent::__construct();
        // Nota: Si MainController necesita un modelo (ej. para cargar datos de perfil), 
        // el padre lo carga automáticamente si existe Main_model.php.
    }

    /**
     * Muestra la página principal a la que solo los usuarios autenticados pueden acceder.
     * Esta función se llama al acceder a URL/Main/Main.
     */
    public function Main()
    {
        // 1. VALIDACIÓN DE AUTENTICACIÓN
        // Verifica si la sesión 'User' existe. Si no, redirige al login.
        if (!Session::getSession('User')) {
            // Establece un mensaje de error si es necesario (opcional)
            Session::setSession('alert_message', [
                'type' => 'error',
                'title' => 'Acceso Denegado',
                'text' => 'Debes iniciar sesión para acceder a esta página.'
            ]);
            
            // Redirige a la página de login (URL/Index o URL/)
            header('Location: ' . URL);
            exit;
        }

        // 2. PREPARACIÓN DE DATOS (Opcional, si Main necesita datos de la DB)
        // Puedes pasar datos a la vista, por ejemplo, el nombre del usuario de la sesión.
        $userName = Session::getSession('User')['full_name'] ?? 'Usuario';
        
        $data = [
            'title' => 'Página Principal',
            'user' => $userName,
            'user_data' => Session::getSession('User') // Pasa toda la info de sesión
        ];

        // 3. RENDERIZACIÓN DE LA VISTA
        // Carga la vista que se encuentra en Views/Main/main.php
        $this->view->render($this, "main", $data); 
    }

    // Puedes añadir otros métodos aquí (ej. Profile, Settings, etc.)
}
