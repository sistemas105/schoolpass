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

    if (!$token) {
        die('QR inválido');
    }

    $data = json_decode(base64_decode($token), true);

    if (!$data || !isset($data['type'])) {
        die('Token corrupto');
    }

    $time = $data['time'] ?? 0;

    // ⏱️ Caducidad: 30 segundos
    if (time() - $time > 600) {
        die('QR expirado');
    }

    /* ===============================
       QR DEL USUARIO PRINCIPAL
       =============================== */
    if ($data['type'] === 'MAIN_USER') {

        $userId = $data['userId'];

        $checkHash = hash(
            'sha256',
            $userId . $time . SECRET_KEY
        );

        if ($checkHash !== $data['hash']) {
            die('QR no válido');
        }

        $this->view->render($this, 'verifyqr', [
            'name'  => 'Tutor principal autorizado',
            'photo' => null
        ]);
        return;
    }

    /* ===============================
       QR DE CONTACTO
       =============================== */
    if ($data['type'] === 'CONTACT') {

        $contactId = $data['contactId'];
        $userId    = $data['userId'];

        $checkHash = hash(
            'sha256',
            $contactId . $userId . $time . SECRET_KEY
        );

        if ($checkHash !== $data['hash']) {
            die('QR no válido');
        }

        $contact = $this->model->getContactForUser($contactId, $userId);

        if (!$contact) {
            die('Contacto no encontrado');
        }

        $this->view->render($this, 'verifyqr', [
            'name'  => $contact['full_name'],
            'photo' => $contact['photo_path']
        ]);
        return;
    }

    die('Tipo de QR no reconocido');
}

}
