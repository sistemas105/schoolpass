<?php $user = $model1['user']; 

$URL = defined('URL') ? URL : '/';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow rounded-4">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Mi Perfil</h4>
                </div>

                <div class="card-body text-center">
                    <img 
    src="<?php echo !empty($user['photo_path']) 
        ? htmlspecialchars($user['photo_path']) 
        : URL . 'Resource/images/user_default.png'; ?>" 
    class="rounded-circle mb-3"
    width="120"
    height="120"
    style="object-fit: cover;"
>

                    <form method="POST" action="<?= $URL ?>Family/UpdateProfile" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto de perfil</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>

                        <button class="btn btn-success w-100">
                            Guardar cambios
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
