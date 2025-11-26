
<?php
// Obtener el mensaje de la sesión
$alert = Session::getSession('alert_message');

if (!empty($alert)) {
    // Convertir el array de PHP a una cadena JSON para usar en JavaScript
    $alert_json = json_encode($alert);
    
    // Eliminar el mensaje de la sesión para que no se muestre de nuevo
    Session::setSession('alert_message', null);
?>
<script>
    // Ejecutar SweetAlert2 después de que la página cargue
    document.addEventListener('DOMContentLoaded', function() {
        const alertData = <?php echo $alert_json; ?>;
        
        Swal.fire({
            icon: alertData.type,
            title: alertData.title,
            text: alertData.text,
            confirmButtonText: 'Entendido'
        });
    });
</script>
<?php
} // Cierre del if(!empty($alert))
// ... el resto de tu vista de login HTML
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            
            <div class="card text-white shadow-lg border-0 rounded-4" style="background-color: #06326b;">
                
                <div class="card-header bg-transparent text-center border-0 pt-4 pb-0">
                    <img src="<?php echo URL.RQ ?>images/logo.png" class="mx-auto w-50 mb-3" alt="Logo School Pass">
                    <h2 class="fw-bold mb-4">Iniciar Sesión</h2>
                </div>
                                    
                <div class="card-body pt-0 px-4 pb-4">
                    
                    <form action="Index/Login" method="POST">
                        
                        <div class="mb-3">
                            <label for="emailInput" class="form-label visually-hidden">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="emailInput"
                                placeholder="Correo Electrónico" 
                                class="form-control form-control-lg" 
                                value="<?php echo $model1->Email ?? "" ?>" 
                                onkeypress="new User().ClearMessages(this);"
                            />
                            <span id="email" class="text-warning small"><?php echo $model2->Email ?? "" ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <label for="passwordInput" class="form-label visually-hidden">Contraseña</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="passwordInput"
                                placeholder="Contraseña" 
                                class="form-control form-control-lg" 
                                value="<?php echo $model1->Password ?? "" ?>" 
                                onkeypress="new User().ClearMessages(this);"
                            />
                            <span id="password" class="text-warning small"><?php echo $model2->Password ?? "" ?></span>
                        </div>
                        
                        <div class="mb-3 text-center">
                            <span class="text-danger small fw-bold">
                                <?php echo $model2->Role ?? "" ?>
                            </span>
                        </div>
                        
                        <button type="submit" class="btn btn-lg w-100 mb-3" style="background-color: #46b258; border-color: #46b258;">
                            Ingresar
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo URL ?>Index/Register" class="btn btn-outline-light w-100">
                            ¿No tienes cuenta? Regístrate aquí
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>