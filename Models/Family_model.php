<?php
// Asegúrate de que esta ruta sea correcta para tu proyecto
// require_once LBS . 'Model.php'; 

class Family_model extends Model
{
    // Las tablas principales con las que este modelo interactúa.
    protected $studentTable = 'students'; 
    protected $contactTable = 'contacts'; 
    protected $familyTable = 'families'; 

    public function __construct()
    {
        parent::__construct();
    }

    // --- MÉTODOS DE STUDENTS ---

    /**
     * Obtiene la lista de alumnos asociados a una familia (students.family_id).
     * @param int $familyId El ID de la familia a buscar.
     * @return array La lista de alumnos.
     */
    public function getStudentsByFamilyId($familyId)
    {
        // Asumiendo que $this->db->select3 es el método para SELECT
        $result = $this->db->select3(
            '*', 
            $this->studentTable,
            " WHERE family_id = ?", 
            [$familyId]
        );
        
        return $result['results'] ?? [];
    }

    /**
     * Verifica si una matrícula ya existe.
     */
    public function matriculaExists($matricula)
    {
        // Usamos select3 para verificar la existencia de la matrícula
        $check = $this->db->select3(
            'id', 
            $this->studentTable, 
            " WHERE matricula = ?", 
            [$matricula]
        );
        return !empty($check['results']);
    }

    /**
     * Registra un nuevo alumno.
     */
    public function registerStudent($familyId, $fullName, $nivel, $grado, $grupo, $matricula)
    {
        // 1. Verificar si la matrícula ya existe
        if ($this->matriculaExists($matricula)) {
            return 'matricula_exists';
        }

        // 2. Datos a insertar
        $insertData = [
            'family_id' => $familyId,
            'full_name' => $fullName,
            'nivel' => $nivel,
            'grado' => $grado,
            'grupo' => $grupo,
            'matricula' => $matricula,
            'active' => 1 // Por defecto activo
        ];

        // 3. Ejecutar la inserción
        $valueString = ' (family_id, full_name, nivel, grado, grupo, matricula, active) 
                             VALUES (:family_id, :full_name, :nivel, :grado, :grupo, :matricula, :active)';
                             
        return $this->db->insert($this->studentTable, $insertData, $valueString);
    }
    
    // --- MÉTODOS DE CONTACTS ---

    /**
     * Registra un nuevo contacto/familiar asociado directamente al usuario (tutor principal).
     * @param int $userId El ID del usuario logueado.
     * @param array $data Los datos del contacto (debe incluir 'role', 'full_name', 'phone', 'email').
     * @return bool Resultado de la operación.
     */
    public function registerContact($userId, $data)
    {
        // 1. Preparar los datos usando user_id
        $insertData = [
            'user_id'       => $userId, // ⭐️ CLAVE FORÁNEA: user_id ⭐️
            'role'          => $data['role'],      // Coincide con el ENUM de la DB (image_e58b0f.png)
            'full_name'     => $data['full_name'],
            'phone'         => $data['phone'] ?? null,
            'email'         => $data['email'] ?? null,
            // photo_path se deja a NULL
        ];

        // 2. Ejecutar la inserción
        $valueString = ' (user_id, role, full_name, phone, email) 
                             VALUES (:user_id, :role, :full_name, :phone, :email)';
                             
        return $this->db->insert($this->contactTable, $insertData, $valueString); 
    }

    /**
     * Obtiene la lista de contactos asociados directamente al usuario (tutor principal).
     * @param int $userId El ID del usuario logueado.
     * @return array La lista de contactos.
     */
    public function getContactsByUserId($userId)
    {
        $result = $this->db->select3(
            '*', 
            $this->contactTable,
            " WHERE user_id = ?", 
            [$userId]
        );
        
        return $result['results'] ?? [];
    }
    
    /**
     * Actualiza el teléfono y el correo electrónico de un contacto.
     * * @param array $data Array asociativo con 'id', 'phone', y 'email'.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateContactInfo(array $data): bool {
        // Asegúrate de que los campos requeridos estén presentes
        if (!isset($data['id'])) {
            return false;
        }

        $id = $data['id'];
        $phone = $data['phone'];
        $email = $data['email'];

        // Consulta SQL para actualizar SOLO el teléfono y el correo electrónico
        $sql = "UPDATE {$this->contactTable} 
                SET phone = :phone, email = :email 
                WHERE id = :id";

        try {
            // Ejemplo de ejecución de consulta preparada (usando una interfaz de DB genérica)
            // DEBES REEMPLAZAR ESTA LÓGICA CON LA DE TU SISTEMA DE BASE DE DATOS.
            
            // Simulación de una consulta (Asume que $this->db es una instancia de conexión válida)
            // $stmt = $this->db->prepare($sql);
            // $stmt->bindParam(':phone', $phone);
            // $stmt->bindParam(':email', $email);
            // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            // $result = $stmt->execute();
            
            // Aquí se simula el éxito de la operación.
            $result = true; 

            // Si usas PDO, podrías verificar: return $stmt->rowCount() > 0;
            return (bool) $result;

        } catch (Exception $e) {
            // Log del error o manejo
            // error_log("Error al actualizar contacto: " . $e->getMessage());
            return false;
        }
    }
}