<?php
// Asegúrate de que las constantes de conexión (USER, PASS, DB) 
// están definidas en tu config.php
class Model 
{
    protected $db; // Almacenará la instancia de QueryManager

   public function __construct() {
        
        // Aseguramos que la clase Connection se carga, si no lo hace el autoloader
        require_once LBS . 'Connection.php'; 

        // Creamos una instancia de Connection
        $connection = new Connection(); 
        
        // Asignamos el QueryManager que está dentro de la instancia de Connection
        // Asumo que QueryManager se inicializa dentro del constructor de Connection.
        // La clase Connection debe exponer el QueryManager.
        $this->db = $connection->db; 

        // ELIMINA la lógica de verificar constantes, ya no es necesaria
    }
    // --- MÉTODOS ADAPTADOS DE QueryManager ---

    public function selectOne($table, array $whereParams)
    {
        // Ejemplo de adaptación para select1: SELECT * FROM tabla WHERE campo1 = ?
        $keys = array_keys($whereParams);
        $where = " WHERE " . implode(" = ? AND ", $keys) . " = ?";
        
        $params = array_values($whereParams);
        
        $result = $this->db->select1('*', $table, $where, $params);
        
        if (is_array($result) && !empty($result['results'])) {
            return $result['results'][0]; // Retorna una sola fila
        }
        return null;
    }
    
    // Método para insertar (adaptado a la necesidad de Index_model)
 public function insertRow($table, array $data)
{
    $fields = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data)); // Debe ser ":full_name, :email, ..."
    
    // 🛑 ATENCIÓN AQUÍ: Asegúrate que el espacio antes de "(" sea correcto
    $values_str = " (" . $fields . ") VALUES (" . $placeholders . ")"; 
    
    $params = $data; 
    
    return $this->db->insert($table, $params, $values_str); 
}
    
    // Método para contar (similar a select1)
    public function countRows($table, array $whereParams)
    {
         $keys = array_keys($whereParams);
         $where = " WHERE " . implode(" = ? AND ", $keys) . " = ?";
         $params = array_values($whereParams);
         
         $result = $this->db->select1('COUNT(*) as count', $table, $where, $params);
         
         if (is_array($result) && !empty($result['results'])) {
             return (int)$result['results'][0]['count'];
         }
         return 0;
    }
}
?>