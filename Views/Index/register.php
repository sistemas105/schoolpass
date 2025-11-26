<?php
    $model1 = Session::getSession('model1') ?? (object)['FullName' => '', 'Email' => ''];
    $model2 = Session::getSession('model2') ?? (object)['FullName' => '', 'Email' => '', 'Password' => '', 'ConfirmPassword' => '', 'Role' => ''];
    
    // Limpia la sesión para que los errores y datos no persistan tras un intento fallido
    Session::setSession('model1', null);
    Session::setSession('model2', null);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card text-white shadow-lg border-0 rounded-4" style="background-color: #06326b;">
                
                <div class="card-header bg-transparent text-center border-0 pt-4 pb-0">
                    <h2 class="fw-bold mb-4">Registro de Usuario</h2>
                </div>
                                    
                <div class="card-body pt-0 px-4 pb-4">
                    
                    <?php if (!empty($model2->Role)): ?>
                        <div class="alert alert-danger text-center small fw-bold" role="alert">
                            <?php echo $model2->Role; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo URL ?>Index/CreateUser" method="POST">
                        
                        <div class="mb-3">
                            <label for="fullNameInput" class="form-label visually-hidden">Nombre Completo</label>
                            <input 
                                type="text" 
                                name="full_name" 
                                id="fullNameInput"
                                placeholder="Nombre Completo" 
                                class="form-control form-control-lg" 
                                value="<?php echo $model1->FullName ?? "" ?>" 
                                required
                            />
                            <span class="text-warning small"><?php echo $model2->FullName ?? "" ?></span>
                        </div>

                        <div class="mb-3">
                            <label for="emailInput" class="form-label visually-hidden">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="emailInput"
                                placeholder="Correo Electrónico" 
                                class="form-control form-control-lg" 
                                value="<?php echo $model1->Email ?? "" ?>" 
                                required
                            />
                            <span class="text-warning small"><?php echo $model2->Email ?? "" ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="passwordInput" class="form-label visually-hidden">Contraseña</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="passwordInput"
                                placeholder="Contraseña (mín. 8 caracteres)" 
                                class="form-control form-control-lg" 
                                required
                            />
                            <span class="text-warning small"><?php echo $model2->Password ?? "" ?></span>
                        </div>

                        <div class="mb-4">
                            <label for="confirmPasswordInput" class="form-label visually-hidden">Confirmar Contraseña</label>
                            <input 
                                type="password" 
                                name="confirm_password" 
                                id="confirmPasswordInput"
                                placeholder="Confirmar Contraseña" 
                                class="form-control form-control-lg" 
                                required
                            />
                            <span class="text-warning small"><?php echo $model2->ConfirmPassword ?? "" ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-lg w-100 mb-3" style="background-color: #46b258; border-color: #46b258;">
                            Crear Cuenta
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo URL ?>" class="btn btn-outline-light w-100">
                            ¿Ya tienes cuenta? Ir a Iniciar Sesión
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>