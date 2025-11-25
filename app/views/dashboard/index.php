<?php include '../app/views/layouts/header.php'; ?>

<div class="card shadow">
  <div class="card-header bg-info text-white">Dashboard</div>
  <div class="card-body">
    <h5>Bienvenido a SchoolPass</h5>
    <p>Desde aquí puedes registrar alumnos, contactos y generar tu QR diario.</p>
    <a href="/students/create" class="btn btn-success">Registrar Alumno</a>
    <a href="/contacts" class="btn btn-warning">Registrar Contactos</a>
    <a href="/qr/generate" class="btn btn-primary">Generar QR</a>
  </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
