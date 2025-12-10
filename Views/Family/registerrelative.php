<?php
// Este archivo contiene la vista (HTML/PHP/JS) para la gestión de contactos autorizados.
// Dependencias esperadas:
// 1. Clase global 'Session' con métodos getSession, deleteSession/removeSession.
// 2. Variable global '$model1' conteniendo el array de contactos.
// 3. Constante global 'URL' para la URL base.

// =========================================================
// FUNCIÓN DE TRADUCCIÓN DE ROL
// =========================================================
/**
 * Traduce las claves de rol de la DB a nombres amigables para el usuario.
 * @param string $role Clave del rol (e.g., 'parent_primary').
 * @return string Nombre del rol traducido.
 */
function translateRole($role) {
    switch ($role) {
        case 'parent_primary':
            return 'Tutor Principal';
        case 'parent_secondary':
            return 'Tutor Secundario';
        case 'emergency':
            return 'Contacto de Emergencia';
        case 'familiar':
            return 'Familiar / Otro';
        default:
            // Por defecto, capitalizar y reemplazar guiones bajos por espacios.
            return ucfirst(str_replace('_', ' ', $role));
    }
}

// ⭐️ ACCESO A DATOS: Obtenemos la lista de contactos (Ajusta la variable según tu framework) ⭐️
$contacts_list = $model1['contacts'] ?? []; 

// LÓGICA PARA RESTRINGIR ROLES EN LA VISTA 
$roles_existentes = array_column($contacts_list, 'role');
$tiene_familiar = in_array('familiar', $roles_existentes);
$tiene_emergency = in_array('emergency', $roles_existentes);

// Si ya tiene Familiar Y Contacto de Emergencia, deshabilitamos la adición.
$can_add_new_contact = !($tiene_familiar && $tiene_emergency);


// Función auxiliar para mostrar mensajes de alerta (Compatible con Bootstrap 5)
function displayAlert() {
    // Usamos el namespace de la clase Session si es posible, o asumimos que es global.
    $alert_class = class_exists('Session') ? 'Session' : null;

    if ($alert_class) {
        // Intentar obtener el mensaje de sesión
        $alert = $alert_class::getSession('alert_message');
        
        if ($alert) {
            // Sanitizar la salida antes de imprimir
            $type = htmlspecialchars($alert['type'] ?? 'info');
            $title = htmlspecialchars($alert['title'] ?? 'Alerta');
            $text = htmlspecialchars($alert['text'] ?? '');
            
            echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
            echo '<strong>' . $title . '</strong> ' . $text;
            // Usar btn-close y data-bs-dismiss para B5
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'; 
            echo '</div>';
            
            // Limpiar la sesión después de mostrar
            if (method_exists($alert_class, 'deleteSession')) {
                $alert_class::deleteSession('alert_message');
            } elseif (method_exists($alert_class, 'removeSession')) {
                $alert_class::removeSession('alert_message');
            } 
        }
    }
}

$URL = defined('URL') ? URL : '/'; 
// Obtener el ID del usuario actual de forma segura
$current_user_id = Session::getSession('User')['id'] ?? 'N/A';
?>

<!-- Carga la librería QRCode.js -->
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
<!-- Carga de la librería de Bootstrap 5 (asumiendo que ya está cargada globalmente, pero la incluimos para el modal JS) -->
<!-- Si Bootstrap no está globalmente accesible en la ventana, se debe cargar el script de Bootstrap 5 aquí.
     Ejemplo: <script src=".../bootstrap.bundle.min.js"></script> -->

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-primary mb-4">Gestión de Contactos Autorizados para Recogida</h1>
            
            <?php displayAlert(); ?>

            <!-- Contenedor para los botones de acción superior -->
            <div class="d-flex justify-content-between flex-wrap mb-3">
                
                <!-- Botón para ABRIR MODAL: SE MUESTRA SOLO SI SE PUEDEN AGREGAR MÁS CONTACTOS -->
                <?php if ($can_add_new_contact): ?>
                    <button id="btn-agregar-contacto" class="btn btn-success mb-2 mb-md-0" type="button" 
                        data-bs-toggle="modal" 
                        data-bs-target="#newContactModal">
                        <i class="fas fa-user-plus"></i> + Agregar Nuevo Contacto
                    </button>
                    <!-- Agregamos un pequeño espaciador si el botón de agregar está presente -->
                    <div class="mx-2 d-none d-md-block"></div>
                <?php endif; ?>
                
                <!-- BOTÓN QR DEL TUTOR PRINCIPAL (USUARIO ACTUAL) -->
                <a href="<?php echo htmlspecialchars($URL); ?>Family/GenerateQRCode" class="btn btn-primary" role="button">
                    <i class="fas fa-qrcode"></i> Generar mi Código QR
                </a>
            </div>


            <!-- TABLA DE CONTACTOS REGISTRADOS -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Contactos Registrados (Tutor ID: 
                        <?php echo htmlspecialchars($current_user_id); ?>
                    )</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <!-- Columna de acciones -->
                                    <th class="text-center">Acciones</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($contacts_list) && is_array($contacts_list)) { 
                                    foreach ($contacts_list as $contact) {
                                        $translatedRole = translateRole($contact['role'] ?? '');
                                        // Sanitización de todas las variables
                                        $contactName = htmlspecialchars($contact['full_name'] ?? 'Sin Nombre'); 
                                        $contactId = htmlspecialchars($contact['id'] ?? ''); 
                                        $contactPhone = htmlspecialchars($contact['phone'] ?? '');
                                        $contactEmail = htmlspecialchars($contact['email'] ?? '');
                                        $contactRole = htmlspecialchars($translatedRole);

                                        echo '<tr>';
                                        
                                        // Datos de Contacto (con data-label para responsive)
                                        echo '<td data-label="Nombre">' . $contactName . '</td>';
                                        echo '<td data-label="Rol">' . $contactRole . '</td>'; 
                                        echo '<td data-label="Teléfono">' . ($contactPhone ?: 'N/A') . '</td>';
                                        echo '<td data-label="Email">' . ($contactEmail ?: 'N/A') . '</td>';
                                        
                                        // Columna de Acciones 
                                        echo '<td data-label="Acciones" class="text-center">
                                            <!-- Botón para generar QR de este contacto -->
                                            <button class="btn-qr btn-sm btn-success me-1 mb-1 mb-md-0" 
                                                onclick="generateQR(\'' . $contactId . '\', \'' . $contactName . '\')">
                                                <i class="fas fa-qrcode"></i> QR
                                            </button>
                                            
                                            <!-- Botón Editar -->
                                            <button class="btn btn-sm btn-info" 
                                                onclick="editContact(\'' . $contactId . '\', \'' . $contactPhone . '\', \'' . $contactEmail . '\', \'' . $contactName . '\')">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                        </td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted">No hay contactos autorizados registrados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- MODAL DE REGISTRO DE CONTACTO (Bootstrap 5) -->
<!-- ============================================= -->
<div class="modal fade" id="newContactModal" tabindex="-1" aria-labelledby="newContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newContactModalLabel">Registrar Nuevo Familiar/Contacto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- El formulario apunta a la acción de creación -->
            <form action="<?php echo htmlspecialchars($URL); ?>Family/CreateRelative" method="POST">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="full_name" class="form-label">Nombre Completo del Contacto</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Rol / Parentesco</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Selecciona el rol...</option>
                            
                            <!-- LÓGICA DE OPCIONES: Solo muestra los roles que aún no existen -->
                            <?php if (!$tiene_familiar): ?>
                                <option value="familiar">Familiar (Abuelo/Tío)</option>
                            <?php endif; ?>

                            <?php if (!$tiene_emergency): ?>
                                <option value="emergency">Contacto de Emergencia</option>
                            <?php endif; ?>

                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="phone" class="form-label">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo Electrónico (Opcional)</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Contacto</button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<!-- ============================================= -->
<!-- MODAL DE EDICIÓN (SOLO TELÉFONO Y CORREO) -->
<!-- ============================================= -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <!-- Título actualizado por JS -->
                <h5 class="modal-title" id="editContactModalLabel">Editar Contacto</h5> 
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- El formulario apunta a la acción de edición -->
            <form id="editContactForm" action="<?php echo htmlspecialchars($URL); ?>Family/UpdateRelative" method="POST">
                <!-- Campo oculto para el ID del contacto que se está editando -->
                <input type="hidden" name="contact_id" id="edit_contact_id">

                <div class="modal-body">
                    <p class="text-muted">Estás editando solo la información de contacto (Teléfono y Email).</p>
                    
                    <div class="form-group mb-3">
                        <label for="edit_phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-warning">Actualizar Contacto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- MODAL PARA MOSTRAR EL QR -->
<!-- ============================================= -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="qrCodeModalLabel">Código QR de Recogida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="qr_person_name" class="fw-bold text-success fs-5">Cargando...</p>
                <!-- Aquí se inyectará la imagen del QR -->
                <div id="qrcode_container" class="my-3 d-flex justify-content-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Generando Código QR...</span>
                    </div>
                </div>
                <p class="text-muted small">Muestra este código al personal de la escuela para la recogida.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Variable global para la URL base (definida en PHP)
 */
const BASE_URL = '<?php echo $URL; ?>';

/**
 * Función para inicializar y mostrar el modal de edición con los datos del contacto.
 * @param {string} id ID del contacto
 * @param {string} phone Teléfono actual
 * @param {string} email Correo electrónico actual
 * @param {string} name Nombre completo del contacto (para fines de UX)
 */
function editContact(id, phone, email, name) {
    // 1. Cargar los datos en el formulario de edición
    document.getElementById('edit_contact_id').value = id;
    document.getElementById('edit_phone').value = phone || ''; // Usar cadena vacía si es null/undefined
    document.getElementById('edit_email').value = email || ''; // Usar cadena vacía si es null/undefined
    
    // 2. Actualizar el título del modal para el contexto
    document.getElementById('editContactModalLabel').innerText = 'Editar Contacto: ' + name;

    // 3. Mostrar el modal de edición (usando las funciones nativas de Bootstrap 5)
    // Se asume que 'bootstrap' está disponible globalmente.
    var editModal = new bootstrap.Modal(document.getElementById('editContactModal'));
    editModal.show();
}

/**
 * Función que realiza una llamada AJAX para obtener el token QR de un contacto y lo muestra en un modal.
 * Utiliza fetch para la llamada asíncrona y la librería QRCode.js para la generación.
 * @param {string} contactId ID del contacto
 * @param {string} contactName Nombre del contacto (para visualización)
 */

//funcion qr para contactos
async function generateQR(contactId, contactName) {
    var qrModalElement = document.getElementById('qrCodeModal');
    var qrModal = bootstrap.Modal.getInstance(qrModalElement) || new bootstrap.Modal(qrModalElement);
    qrModal.show();
    
    const qrContainer = document.getElementById('qrcode_container');
    const personNameDisplay = document.getElementById('qr_person_name');

    personNameDisplay.innerText = contactName;

    qrContainer.innerHTML = `
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Generando Código QR...</span>
        </div>
    `;

    try {
        const response = await fetch(`${BASE_URL}Family/GenerateRelativeQRCodeDataAjax`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `contact_id=${encodeURIComponent(contactId)}`
        });

        const result = await response.json();

        if (!result.success) throw new Error(result.message);

        qrContainer.innerHTML = '';

        new QRCode(qrContainer, {
            text: result.qr_token,
            width: 200,
            height: 200,
            colorDark : "#198754",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

    } catch (error) {
        qrContainer.innerHTML = `<p class="text-danger small">Error: ${error.message}</p>`;
        personNameDisplay.innerText = "Error al generar";
    }
}

</script>