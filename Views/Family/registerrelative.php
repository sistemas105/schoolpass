<?php
// =========================================================
// FUNCIÓN DE TRADUCCIÓN DE ROL
// =========================================================
// Traduce las claves de la DB a nombres amigables para el usuario.
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
    // La corrección del error fatal anterior se mantiene
    if (class_exists('Session') && ($alert = Session::getSession('alert_message'))) {
        echo '<div class="alert alert-' . htmlspecialchars($alert['type']) . ' alert-dismissible fade show" role="alert">';
        echo '<strong>' . htmlspecialchars($alert['title']) . '</strong> ' . htmlspecialchars($alert['text']);
        // Usar btn-close y data-bs-dismiss para B5
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'; 
        echo '</div>';
        
        // CORRECCIÓN DE ERROR FATAL: Intentar métodos conocidos para limpiar la sesión
        if (method_exists('Session', 'deleteSession')) {
            Session::deleteSession('alert_message');
        } elseif (method_exists('Session', 'removeSession')) {
            Session::removeSession('alert_message');
        } 
    }
}

$URL = defined('URL') ? URL : '/'; 
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-primary mb-4">Gestión de Contactos Autorizados para Recogida</h1>
            
            <?php displayAlert(); ?>

            <!-- Contenedor para los botones de acción superior (flex para alinear) -->
            <div class="d-flex justify-content-between flex-wrap mb-3">
                
                <!-- Botón para ABRIR MODAL: SE MUESTRA SOLO SI SE PUEDEN AGREGAR MÁS CONTACTOS -->
                <?php if ($can_add_new_contact): ?>
                    <button id="btn-agregar-contacto" class="btn btn-success" type="button" 
                        data-bs-toggle="modal" 
                        data-bs-target="#newContactModal" 
                        aria-expanded="false" 
                        aria-controls="newContactModal">
                        + Agregar Nuevo Contacto
                    </button>
                    <!-- Agregamos un pequeño espaciador si el botón de agregar está presente -->
                    <div class="mx-2 d-none d-md-block"></div>
                <?php endif; ?>
                
                <!-- BOTÓN QR DEL TUTOR PRINCIPAL (USUARIO ACTUAL) -->
                <button class="btn-qr btn-primary" onclick="generateQR('user_<?php echo Session::getSession('User')['id'] ?? 'N/A'; ?>')">
                    Generar mi Código QR
                </button>
            </div>


            <!-- TABLA DE CONTACTOS REGISTRADOS -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Contactos Registrados (Tutor ID: 
                        <?php echo Session::getSession('User')['id'] ?? 'N/A'; ?>
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
                                        $translatedRole = translateRole($contact['role']);
                                        $contactName = htmlspecialchars($contact['full_name']); // Nombre del contacto
                                        $contactId = htmlspecialchars($contact['id']); // ID del contacto

                                        echo '<tr>';
                                        
                                        // Datos de Contacto (con data-label para responsive)
                                        echo '<td data-label="Nombre">' . $contactName . '</td>';
                                        echo '<td data-label="Rol">' . htmlspecialchars($translatedRole) . '</td>'; 
                                        echo '<td data-label="Teléfono">' . htmlspecialchars($contact['phone'] ?? 'N/A') . '</td>';
                                        echo '<td data-label="Email">' . htmlspecialchars($contact['email'] ?? 'N/A') . '</td>';
                                        
                                        // Columna de Acciones (con data-label para responsive)
                                        echo '<td data-label="Acciones" class="text-center">
                                                    <!-- Botón para generar QR de este contacto -->
                                                    <button class="btn-qr btn-sm btn-success me-1" 
                                                        onclick="generateQR(\'contact_' . $contactId . '\')">
                                                        <i class="fas fa-qrcode"></i> QR
                                                    </button>
                                                    
                                                    <!-- Botón Editar (Se pasó el nombre para mejor UX en el modal) -->
                                                    <button class="btn btn-sm btn-info" 
                                                        onclick="editContact(\'' . $contactId . '\', \'' . htmlspecialchars($contact['phone'] ?? '') . '\', \'' . htmlspecialchars($contact['email'] ?? '') . '\', \'' . $contactName . '\')">
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
<!-- Se mantiene para agregar nuevos contactos -->
<!-- ============================================= -->
<div class="modal fade" id="newContactModal" tabindex="-1" aria-labelledby="newContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newContactModalLabel">Registrar Nuevo Familiar/Contacto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- La etiqueta <form> envuelve todo el cuerpo y el pie para capturar los datos del formulario -->
            <form action="<?php echo $URL; ?>Family/CreateRelative" method="POST">
                <div class="modal-body">
                    <!-- Contenido del formulario -->
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
                <!-- El título se actualizará con el nombre del contacto vía JavaScript -->
                <h5 class="modal-title" id="editContactModalLabel">Editar Contacto</h5> 
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- El formulario apunta a la acción de edición (DEBES IMPLEMENTARLA en tu controlador) -->
            <form id="editContactForm" action="<?php echo $URL; ?>Family/UpdateRelative" method="POST">
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
                <p id="qr_person_name" class="fw-bold text-success fs-5"></p>
                <!-- Aquí se inyectará la imagen del QR -->
                <div id="qrcode_container" class="my-3 d-flex justify-content-center">
                    <!-- Placeholder visual mientras se genera -->
                    <img src="https://placehold.co/200x200/00bcd4/ffffff?text=Cargando+QR" alt="Cargando QR" class="img-fluid rounded shadow">
                </div>
                <p class="text-muted small">Muestra este código al personal de la escuela para la recogida.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Librería de Font Awesome para iconos (Asumiendo que está disponible o se carga en el head) -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->

<script>
/**
 * Script para manejar el modal de edición y cargar los datos del contacto.
 * Requiere que Bootstrap 5 esté cargado en tu página.
 * * @param {string} id ID del contacto
 * @param {string} phone Teléfono actual
 * @param {string} email Correo electrónico actual
 * @param {string} name Nombre completo del contacto (para fines de UX)
 */
function editContact(id, phone, email, name) {
    // 1. Cargar los datos en el formulario de edición
    document.getElementById('edit_contact_id').value = id;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    
    // 2. Actualizar el título del modal para el contexto
    document.getElementById('editContactModalLabel').innerText = 'Editar Contacto: ' + name;

    // 3. Mostrar el modal de edición (usando las funciones nativas de Bootstrap 5)
    var editModal = new bootstrap.Modal(document.getElementById('editContactModal'));
    editModal.show();
}

/**
 * Función (placeholder) para generar y mostrar el QR.
 * NECESITARÁS una librería de QR como QRCode.js o similar para la implementación final.
 * * @param {string} identifier ID del contacto o 'user_[ID]' para el tutor principal.
 */
function generateQR(identifier) {
    // 1. Obtener la referencia al modal
    var qrModalElement = document.getElementById('qrCodeModal');
    var qrModal = new bootstrap.Modal(qrModalElement);
    qrModal.show();
    
    // 2. Determinar el nombre a mostrar
    let personName = '';
    const userIdMatch = identifier.match(/^user_(.*)/);
    const contactIdMatch = identifier.match(/^contact_(.*)/);
    
    if (userIdMatch) {
        // En un entorno real, obtendrías el nombre del usuario logeado
        personName = 'Tutor Principal (ID: ' + userIdMatch[1] + ')'; 
    } else if (contactIdMatch) {
        // En un entorno real, harías una llamada AJAX o buscarías el nombre en la tabla de datos cargada.
        // Aquí se usa un placeholder para el nombre ya que no se tiene la data completa en JS.
        // Para mejorar, se podría haber guardado en un data-attribute del botón QR de la tabla.
        personName = 'Contacto Autorizado (ID: ' + contactIdMatch[1] + ')';
    } else {
        personName = 'Código Desconocido';
    }

    document.getElementById('qr_person_name').innerText = personName;

    // 3. Simular la generación del QR (DEBES REEMPLAZAR ESTO con la lógica real)
    const qrContainer = document.getElementById('qrcode_container');
    
    // Limpiar contenedor
    qrContainer.innerHTML = ''; 

    // Placeholder (simula el QR con el ID):
    qrContainer.innerHTML = '<div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando...</span></div>';

    setTimeout(() => {
        // ** Lógica real de generación de QR (Ejemplo con una librería) **
        // Si usas QRCode.js, el código sería algo como:
        // new QRCode(qrContainer, { text: identifier, width: 200, height: 200 });

        // Sustituyendo el spinner por la imagen placeholder del QR
        qrContainer.innerHTML = '<img src="https://placehold.co/200x200/28a745/ffffff?text=' + encodeURIComponent(identifier) + '" alt="Código QR" class="img-fluid rounded shadow">';
    }, 1000); // Simula el tiempo de carga del QR

}
</script>