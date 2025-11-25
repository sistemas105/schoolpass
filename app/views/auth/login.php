
<?php include '../app/views/layouts/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow">
      <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
        <span>Login</span>
        <!-- Link Registrar -->
        <a href="/register" class="text-white small text-decoration-underline">Registrar</a>
      </div>
      <div class="card-body">
        <form method="POST" action="/login">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Ingresar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

