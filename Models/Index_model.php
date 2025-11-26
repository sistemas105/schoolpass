<?php
// Ya no necesitamos require_once, el autoloader de index.php ya cargó Model

class Index_model extends Model 
{
    private $table = "usuarios"; // Reemplaza 'usuarios' si tu tabla se llama diferente

    public function __construct() {
        parent::__construct(); // Llama al constructor de Model para inicializar $this->db
    }

    // Método para insertar un nuevo usuario (para Register)
    public function registerUser($full_name, $email, $password_hash)
{
    // Obtener la fecha y hora actual para el campo created_at
    $current_time = date('Y-m-d H:i:s'); 

    return $this->insertRow($this->table, [
        'full_name' => $full_name,
        'email' => $email,
        'password_hash' => $password_hash,
        'created_at' => $current_time // ¡Añadido explícitamente!
    ]);
}
    // Método para obtener un usuario por email (para Login)
    public function getUserByEmail($email)
    {
        // Usamos el método selectOne que definimos en Model.php
        return $this->selectOne($this->table, ['email' => $email]);
    }

    // Método para verificar si el email ya existe (para Register)
    public function emailExists($email)
    {
        // Usamos el método countRows que definimos en Model.php
        return $this->countRows($this->table, ['email' => $email]) > 0;
    }
}
?>