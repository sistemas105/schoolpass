<?php
// 💡 CORRECCIÓN: Accedemos a los datos desde el array $model1, que contiene los datos del Controller.
$qr_token = $model1['qr_token'] ?? '';
$user_name = $model1['user_name'] ?? 'Usuario';
$URL = defined('URL') ? URL : '/';
?>

<!-- Carga la librería QRCode.js -->
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

<!-- Se ha eliminado la etiqueta <pre> que mostraba el token sin procesar -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg text-center border-0 rounded-4">
                <div class="card-header bg-success text-white py-3">
                    <h2 class="h4 mb-0">Código QR de Recogida Autorizada</h2>
                </div>

                <div class="card-body p-4">
                    <h3 class="fw-bold text-success mb-3">
                        <?= htmlspecialchars($user_name) ?>
                    </h3>

                    <p class="text-muted small">
                        Muestra este código al personal de la escuela para validar la autorización de recogida.
                    </p>

                    <!-- Contenedor para el QR -->
                    <div id="qrcode_output" class="d-flex justify-content-center align-items-center my-4">
                        <!-- Spinner inicial mientras se genera el QR -->
                        <div class="spinner-border text-success"></div>
                    </div>

                    <hr>
                    <p class="small text-danger">
                        ⚠️ Este código es personal. No lo compartas con personas no autorizadas.
                    </p>
                </div>

                <div class="card-footer bg-light border-0">
                    <a href="<?= $URL ?>Family/RegisterRelative" class="btn btn-secondary w-100">
                        Volver a Contactos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 💡 CORRECCIÓN: Accedemos al token desde la variable local de PHP.
    const token = "<?= htmlspecialchars($qr_token, ENT_QUOTES, 'UTF-8') ?>";
    const output = document.getElementById("qrcode_output");

    if (!token) {
        output.innerHTML = "<p class='text-danger'>Error: Token no disponible.</p>";
        return;
    }

    // El URL que se codificará en el QR
    const url = "<?= URL ?>Scan/Verify?token=" + encodeURIComponent(token);
    
    // Limpiar el spinner
    output.innerHTML = "";

    // Generar el código QR
    new QRCode(output, {
        text: url,
        width: 220,
        height: 220,
        colorDark : "#198754", 
        colorLight : "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
});
</script>