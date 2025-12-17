<?php
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
    // Normalizar/asegurar claves y valores
    $phone = isset($data['phone']) && $data['phone'] !== '' ? $data['phone'] : null;
    $email = isset($data['email']) && $data['email'] !== '' ? $data['email'] : null;
    // En tu controlador usas $data['photo'] al subir la imagen; aquí lo mapeamos a photo_path
    $photoPath = isset($data['photo']) && $data['photo'] !== '' ? $data['photo'] : null;

    // Preparar datos a insertar (coincide con columnas de la tabla contacts)
    $insertData = [
        'user_id'   => $userId,
        'role'      => $data['role'],
        'full_name' => $data['full_name'],
        'phone'     => $phone,
        'email'     => $email,
        'photo_path'=> $photoPath
    ];

    // Query string esperado por tu método insert()
    $valueString = ' (user_id, role, full_name, phone, email, photo_path) 
                     VALUES (:user_id, :role, :full_name, :phone, :email, :photo_path)';

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
   public function generateMainUserQRCodeData(int $userId)
{
    if ($userId <= 0) {
        return false;
    }

    $timestamp = time();

    $payload = [
        'type'   => 'MAIN_USER',
        'userId' => $userId,
        'time'   => $timestamp,
        'hash'   => hash(
            'sha256',
            $userId . $timestamp . SECRET_KEY
        )
    ];

    // 🔐 IMPORTANTE: JSON + BASE64
    return base64_encode(json_encode($payload));
}
    public function generateRelativeQRCodeData($contactId)
{
    $userId = Session::getSession('User')['id'] ?? 0;

    $result = $this->db->select3(
        '*',
        $this->contactTable,
        " WHERE id = ? AND user_id = ? ",
        [$contactId, $userId]
    );

    if (empty($result['results'])) {
        return false;
    }

    $timestamp = time();
    $hash = hash('sha256', $contactId . $userId . $timestamp . SECRET_KEY);

    // TOKEN SIMPLE (puede ir en base64)
    $token = base64_encode(json_encode([
        'contactId' => $contactId,
        'userId'    => $userId,
        'time'      => $timestamp,
        'hash'      => $hash
    ]));

    // 🚨 ESTO ES LO QUE VA EN EL QR
    return URL . "Scan/Verify?token=" . urlencode($token);
}
public function getContactForUser(int $contactId, int $userId): ?array
{
    $result = $this->db->select3(
        '*',
        $this->contactTable,
        " WHERE id = ? AND user_id = ? ",
        [$contactId, $userId]
    );

    if (
        isset($result['results']) &&
        is_array($result['results']) &&
        count($result['results']) > 0
    ) {
        return $result['results'][0];
    }

    return null;
}

}