<?php
// Acceso a los datos pasados por el controlador
// CORRECCIÓN: Se espera la clave 'qr_data' del controlador.
$qrDataString = $model1['qr_data'] ?? 'error';
$userName = $model1['user_name'] ?? 'Usuario';
$URL = defined('URL') ? URL : '/'; 
?>

<!-- Carga la librería QRCode.js (Es la forma más sencilla de renderizar QR en cliente con JS) -->
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg text-center border-0 rounded-4">
                <div class="card-header bg-success text-white py-3">
                    <h2 class="h4 mb-0">Código QR de Recogida Autorizada</h2>
                </div>
                <div class="card-body p-4">
                    <h3 class="fw-bold text-success mb-3" id="person_name_display"><?php echo htmlspecialchars($userName); ?></h3>
                    
                    <p class="text-muted small">Muestra este código al personal de la escuela para validar la autorización de recogida.</p>
                    
                    <!-- Contenedor donde se renderizará el QR -->
                    <div id="qrcode_output" class="d-flex justify-content-center align-items-center my-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Generando Código QR...</span>
                        </div>
                    </div>
                    
                    <hr>
                    <p class="small text-danger">⚠️ Este código es personal. No lo compartas con personas no autorizadas.</p>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="<?php echo $URL; ?>Family/RegisterRelative" class="btn btn-secondary w-100">
                        Volver a Contactos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrData = "<?php echo $qrDataString; ?>";
    const container = document.getElementById('qrcode_output');
    
    // Limpiar cualquier indicador de carga
    container.innerHTML = ''; 

    if (qrData === 'error') {
        container.innerHTML = '<p class="text-danger">Error al obtener los datos del QR.</p>';
        return;
    }

    try {
        // Inicializar QRCode.js en el contenedor
        new QRCode(container, {
            text: qrData,
            width: 250,
            height: 250,
            colorDark : "#198754", // Color verde Bootstrap
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H // Alta corrección de errores
        });
        
    } catch (e) {
        console.error("Error al generar el QR:", e);
        container.innerHTML = '<p class="text-danger">No se pudo generar la imagen del QR.</p>';
    }
});
</script>