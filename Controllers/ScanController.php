<?php

class ScanController extends Controllers
{
    public $model;

    public function __construct()
    {
        parent::__construct();

        // Cargar modelo manualmente (según tu framework)
      require_once 'Models/Family_model.php';
        $this->model = new Family_model();
    }

  public function Verify()
{
    $token = $_GET['token'] ?? '';

    $log = [
        'user_id'    => null,
        'contact_id' => null,
        'scanned_at' => date('Y-m-d H:i:s'),
        'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
        'agent'      => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'status'     => 'INVALID'
    ];

    if (!$token) {
        $this->model->logQRScan($log);
        die('QR inválido');
    }

    $decoded = json_decode(base64_decode($token), true);
    if (!$decoded) {
        $this->model->logQRScan($log);
        die('Token corrupto');
    }

    $contactId = $decoded['contactId'];
    $userId    = $decoded['userId'];
    $time      = $decoded['time'];
    $hash      = $decoded['hash'];

    $log['user_id']    = $userId;
    $log['contact_id'] = $contactId ?: null;

    // ⏱️ Expiración
    if (time() - $time > 30) {
        $log['status'] = 'EXPIRED';
        $this->model->logQRScan($log);
        die('QR expirado');
    }

    // 🔐 Hash
    $checkHash = hash('sha256', $contactId . $userId . $time . SECRET_KEY);
    if ($checkHash !== $hash) {
        $log['status'] = 'INVALID';
        $this->model->logQRScan($log);
        die('QR no válido');
    }

    // 🔎 Contacto
   if ($contactId > 0) {

    // ▶ AUTORIZADO
    $contact = $this->model->getContactByIdAndUser($contactId, $userId);
    if (!$contact) {
        $log['status'] = 'NOT_FOUND';
        $this->model->logQRScan($log);
        die('Contacto no encontrado');
    }

} else {

    // ▶ TUTOR
    $contact = $this->model->getUserById($userId);
    if (!$contact) {
        $log['status'] = 'NOT_FOUND';
        $this->model->logQRScan($log);
        die('Tutor no encontrado');
    }
}

    // ✅ ÉXITO
    $log['status'] = 'OK';
    $this->model->logQRScan($log);

    $this->view->render($this, 'verifyqr', [
        'name'  => $contact['full_name'],
        'photo' => $contact['photo_path']
    ]);
}

}
