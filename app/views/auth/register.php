<?php include '../app/views/layouts/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow">
      <div class="card-header bg-success text-white">Registro de Familia</div>
      <div class="card-body">
        <form method="POST" action="/register" enctype="multipart/form-data">
          <div class="mb-3">
            <label>Nombre completo</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Foto o INE del padre/madre</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
