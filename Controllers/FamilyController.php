<?php
class FamilyController extends Controllers
{
    public function __construct() {
        parent::__construct();
        // Carga Family_model.php automáticamente gracias al padre
    }

    /**
     * Muestra la vista del formulario para dar de alta un nuevo alumno.
     * Corresponde a la ruta: URL/Family/RegisterStudent
     */
    public function RegisterStudent()
{
    if (!Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $userId = Session::getSession('User')['id'];
    $students = $this->model->getStudentsByUser($userId);

    $this->view->render($this, 'registerstudent', [
        'students' => $students
    ]);
}
    /**
     * Procesa la inserción del nuevo alumno.
     * Corresponde a la ruta: URL/Family/CreateStudent
     */
    public function CreateStudent()
    {
        // 1. Seguridad: Solo acepta POST y verifica autenticación
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::getSession('User')) {
            header('Location: ' . URL);
            exit;
        }

        // 2. Obtener datos y family_id del usuario logueado
       $userId = Session::getSession('User')['id'];
        
        $fullName = trim($_POST['full_name'] ?? '');
        $nivel = trim($_POST['nivel'] ?? '');
        $grado = trim($_POST['grado'] ?? '');
        $grupo = trim($_POST['grupo'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        
        $hasError = false;
        
        // 3. VALIDACIÓN
        if (empty($fullName) || empty($nivel) || empty($matricula) || $userId === 0) {
            // Aquí incluimos la validación de familyId, ya que es crítica para students.
            $hasError = true;
            // Si hay errores, deberías guardar un mensaje de alerta aquí.
        }

        if ($hasError) {
            // Si hay errores, redirige de vuelta al formulario
            header('Location: ' . URL . 'Family/RegisterStudent');
            exit;
        }
        
        // 4. Inserción en la DB
        $result = $this->model->registerStudent(
    $userId,
    $fullName,
    $nivel,
    $grado,
    $grupo,
    $matricula
);
        
        if ($result === 'matricula_exists') {
             Session::setSession('alert_message', [
                 'type' => 'warning',
                 'title' => 'Dato Duplicado',
                 'text' => 'La matrícula ingresada ya existe en el sistema.'
             ]);
             header('Location: ' . URL . 'Family/RegisterStudent');
             exit;
        }
        
        if ($result === true) {
            // ÉXITO
            Session::setSession('alert_message', [
                'type' => 'success',
                'title' => '¡Alumno Registrado! 🎉',
                'text' => 'El alumno ha sido dado de alta correctamente.'
            ]);
            header('Location: ' . URL . 'Main/Main'); 
            exit;
        } else {
            // FALLO DE DB
            Session::setSession('alert_message', [
                'type' => 'error',
                'title' => 'Error de Sistema',
                'text' => 'Ocurrió un error al guardar el alumno. Inténtalo de nuevo.'
            ]);
            header('Location: ' . URL . 'Family/RegisterStudent');
            exit;
        }
    }
    
    /**
     * Muestra la vista de gestión de familiares/contactos.
     * Corresponde a la ruta: URL/Family/RegisterRelative
     */
    public function RegisterRelative()
    {
        if (!Session::getSession('User')) {
            header('Location: ' . URL);
            exit;
        }
        
        // Obtenemos el ID del usuario directamente (el dueño de la cuenta)
        $userId = Session::getSession('User')['id'] ?? 0;
        
        // ⚠️ Nota: Asumimos que la tabla contacts ya usa user_id 
        // y que getContactsByUserId existe en el modelo.
        $contacts = $this->model->getContactsByUserId($userId); 
        
        $data = [
            'contacts' => $contacts
        ];

        // Renderizar la vista (Views/Family/registerrelative.php)
        $this->view->render($this, "registerrelative", $data);
    }

    /**
     * Procesa la inserción del nuevo familiar/contacto.
     * Corresponde a la ruta: URL/Family/CreateRelative
     */
  public function CreateRelative()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $userId = Session::getSession('User')['id'];

    // ====== 1. CAPTURAR DATOS ======
    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'role'      => trim($_POST['role'] ?? ''),
        'phone'     => trim($_POST['phone'] ?? ''),
        'email'     => trim($_POST['email'] ?? '')
    ];

    // Validación
    if (empty($data['full_name']) || empty($data['role'])) {
        Session::setSession('alert_message', [
            'type' => 'error',
            'title' => 'Error de Validación',
            'text' => 'El nombre completo y el parentesco son obligatorios.'
        ]);
        header('Location: ' . URL . 'Family/RegisterRelative');
        exit;
    }

    // ====== 2. SUBIR FOTO DEL CONTACTO ======
    $photoPath = null;

    if (!empty($_FILES['photo']['name'])) {

        // Ruta donde se guardarán las fotos
       $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/Resource/contact_photos/";

        // Asegurar que exista la carpeta
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nombre único
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid("contact_") . "." . strtolower($extension);
        $fullPath = $uploadDir . $fileName;



        // Seguridad: extensiones permitidas
        $validExt = ['jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($extension), $validExt)) {
            Session::setSession('alert_message', [
                'type' => 'error',
                'title' => 'Formato inválido',
                'text' => 'Solo se permiten imágenes JPG o PNG.'
            ]);
            header('Location: ' . URL . 'Family/RegisterRelative');
            exit;
        }

        // Mover el archivo
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath)) {
            // Guardar solo el nombre o la ruta — tú decides. Aquí guardamos la ruta.
            $photoPath = $fullPath;
        }
    }

    // Guardar en el arreglo que enviamos al modelo
    $data['photo'] = $photoPath;

    // ====== 3. INSERTAR EN LA BASE ======
    $success = $this->model->registerContact($userId, $data);

    if ($success) {
        Session::setSession('alert_message', [
            'type' => 'success',
            'title' => 'Contacto Registrado',
            'text' => 'El contacto fue registrado correctamente.'
        ]);
    } else {
        Session::setSession('alert_message', [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'No se pudo guardar el contacto.'
        ]);
    }

    header('Location: ' . URL . 'Family/RegisterRelative');
    exit;
}

    /**
     * Controlador para actualizar el teléfono y correo de un contacto autorizado.
     * Recibe los datos del modal de edición.
     */
    public function UpdateRelative() {
        // 1. Verificar si la solicitud es POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirigir o manejar el error si no es POST
            header('Location: ' . URL . 'Family/RegisterRelative');
            exit;
        }

        // 3. Recibir y sanitizar los datos
        $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);
        
        // ** FIX 1: Eliminar FILTER_SANITIZE_STRING (Deprecated) y usar htmlspecialchars **
        $phone = filter_input(INPUT_POST, 'phone');
        $phone = $phone ? htmlspecialchars(trim($phone), ENT_QUOTES, 'UTF-8') : null;
        
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        // Si el correo no es válido pero el campo no está vacío, sanitizar como string
        if ($email === false && !empty($_POST['email'])) {
            // ** FIX 1: Eliminar FILTER_SANITIZE_STRING (Deprecated) y usar htmlspecialchars **
            $raw_email_string = filter_input(INPUT_POST, 'email', FILTER_DEFAULT); 
            $email = $raw_email_string ? htmlspecialchars(trim($raw_email_string), ENT_QUOTES, 'UTF-8') : null;
        } elseif ($email === false) {
             // Si falla la validación del email y está vacío, forzar a NULL o cadena vacía
             $email = null;
        }

        // 4. Validar datos mínimos
        if (!$contact_id) {
            // Manejar error de ID faltante
            Session::setSession('alert_message', ['type' => 'danger', 'title' => 'Error de Edición', 'text' => 'ID de contacto no proporcionado.']);
            header('Location: ' . URL . 'Family/RegisterRelative');
            exit;
        }

        // 5. Preparar los datos para la actualización
        $data_to_update = [
            'id' => $contact_id,
            'phone' => $phone,
            'email' => $email,
        ];

        // 6. Ejecutar la actualización en la base de datos
        try {
            // LLAMADA REAL AL MODELO Family_model
            $success = $this->model->updateContactInfo($data_to_update); 
            
            // ** FIX 2: Eliminamos la línea de simulación ($success = true;) **
            
            if ($success) {
                Session::setSession('alert_message', ['type' => 'success', 'title' => 'Contacto Actualizado', 'text' => 'El teléfono y correo electrónico han sido actualizados correctamente.']);
            } else {
                Session::setSession('alert_message', ['type' => 'warning', 'title' => 'Error de DB', 'text' => 'No se pudo actualizar el contacto. Verifica los permisos o el ID.']);
            }
            
        } catch (Exception $e) {
            Session::setSession('alert_message', ['type' => 'danger', 'title' => 'Error Crítico', 'text' => 'Ocurrió un error en la actualización: ' . $e->getMessage()]);
        }

        // 7. Redirigir de vuelta a la vista de contactos
        header('Location: ' . URL . 'Family/RegisterRelative');
        exit;
    }
    
    /**
     * Muestra el QR del usuario principal (titular de la cuenta).
     * Corresponde a la ruta: URL/Family/GenerateQRCode
     */
 public function GenerateQRCode()
{
    if (!Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $user = Session::getSession('User');

    $data = [
        'contactId' => 0,
        'userId'    => $user['id'],
        'time'      => time(),
    ];

    $data['hash'] = hash(
        'sha256',
        $data['contactId'] . $data['userId'] . $data['time'] . SECRET_KEY
    );

    // 🔐 TOKEN SEGURO PARA URL / QR
    $token = rtrim(strtr(
        base64_encode(json_encode($data)),
        '+/',
        '-_'
    ), '=');

    // ✅ Pasar datos correctamente a la vista
    $this->view->render($this, 'showqrcode', [
    'qr_token'   => $token,
    'user_name'  => $user['full_name'],
    'user_photo' => !empty($user['photo_path'])
        ? URL . $user['photo_path']
        : URL . 'Resource/images/user_default.png'
]);
}





public function GenerateRelativeQRCodeDataAjax()
{
    header('Content-Type: application/json');

    if (!Session::getSession('User')) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Sesión expirada'
        ]);
        exit;
    }

    $contactId = intval($_POST['contact_id'] ?? 0);

    if ($contactId <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Contacto inválido'
        ]);
        exit;
    }

    $qrData = $this->model->generateRelativeQRCodeData($contactId);

    if (!$qrData) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'El contacto no existe o no pertenece a este usuario'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
       'qr_token' => $qrData // ← esto es lo que se codifica en el QR
    ]);
    exit;
}


public function getContactByIdAndUser($contactId, $userId)
{
    $sql = "SELECT id, full_name, photo_path 
            FROM contacts 
            WHERE id = ? AND user_id = ? LIMIT 1";

    return $this->db->selectOne($sql, [$contactId, $userId]);
}
public function Profile()
{
    if (!Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $userId = Session::getSession('User')['id'];
    $user = $this->model->getUserById($userId);

    $this->view->render($this, 'profile', [
        'user' => $user
    ]);
}
public function UpdateProfile()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $userId = Session::getSession('User')['id'];
    $fullName = trim($_POST['full_name'] ?? '');

    if (empty($fullName)) {
        Session::setSession('alert_message', [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'El nombre no puede estar vacío'
        ]);
        header('Location: ' . URL . 'Family/Profile');
        exit;
    }

    /* ===== SUBIR FOTO ===== */
    $photoPath = null;

    if (!empty($_FILES['photo']['name'])) {

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/Resource/user_photos/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $validExt = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $validExt)) {
            Session::setSession('alert_message', [
                'type' => 'error',
                'title' => 'Formato inválido',
                'text' => 'Solo se permiten imágenes JPG o PNG'
            ]);
            header('Location: ' . URL . 'Family/Profile');
            exit;
        }

        $fileName = "user_" . $userId . "_" . time() . "." . $ext;
        $fullPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath)) {
            $photoPath = "/Resource/user_photos/" . $fileName;
        }
    }

    $this->model->updateUserProfile($userId, $fullName, $photoPath);

    // actualizar sesión
    $userSession = Session::getSession('User');
    $userSession['full_name'] = $fullName;
    if ($photoPath) {
        $userSession['photo_path'] = $photoPath;
    }
    Session::setSession('User', $userSession);

    Session::setSession('alert_message', [
        'type' => 'success',
        'title' => 'Perfil actualizado',
        'text' => 'Los cambios se guardaron correctamente'
    ]);

    header('Location: ' . URL . 'Family/Profile');
    exit;
}
public function UpdateStudent()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::getSession('User')) {
        header('Location: ' . URL);
        exit;
    }

    $userId    = Session::getSession('User')['id'];
    $studentId = (int)($_POST['student_id'] ?? 0);

    if ($studentId <= 0) {
        die('ID inválido');
    }

    $student = $this->model->getStudentByIdAndUser($studentId, $userId);
    if (!$student) {
        die('Acceso no autorizado');
    }

    $photoPath = $student['photo_path'];

    /* 📸 SUBIDA DE FOTO */
    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK
    ) {
        $folder = 'Resource/uploads/students/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            die('Formato de imagen no permitido');
        }

        $fileName = 'student_' . $studentId . '_' . time() . '.' . $ext;
        $fullPath = $folder . $fileName;


        if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath)) {
            $photoPath = $fullPath;
        }
    }

    /* 🧠 UPDATE */
    $this->model->updateStudent(
        $studentId,
        trim($_POST['full_name']),
        trim($_POST['nivel']),
        trim($_POST['grado']),
        trim($_POST['grupo']),
        $photoPath
    );
Session::setSession('alert_message', [
    'type'  => 'success',
    'title' => 'Alumno actualizado',
    'text'  => 'Los datos del alumno se actualizaron correctamente.'
]);
    header('Location: ' . URL . 'Family/RegisterStudent');
    exit;
}


}