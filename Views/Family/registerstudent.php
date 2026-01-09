


<div class="container mt-4">

    <h2>Gestión de Alumnos Registrados</h2>
    
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            ➕ Agregar Nuevo Alumno
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Grupo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($model1['students'])): // Si no hay alumnos (asumiendo que los pasas en $data['students']) ?>
                <tr>
                    <td colspan="7" class="text-center">Aún no hay alumnos registrados bajo esta familia.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($model1['students'] as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['nivel']); ?></td>
                        <td><?php echo htmlspecialchars($student['grado']); ?></td>
                        <td><?php echo htmlspecialchars($student['grupo']); ?></td>
                        <td><?php echo $student['active'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></td>
                        <td>
                           <button 
  class="btn btn-sm btn-info text-white"
  data-bs-toggle="modal"
  data-bs-target="#editStudentModal"
  data-id="<?php echo $student['id']; ?>"
  data-name="<?php echo htmlspecialchars($student['full_name']); ?>"
  data-nivel="<?php echo $student['nivel']; ?>"
  data-grado="<?php echo $student['grado']; ?>"
  data-grupo="<?php echo $student['grupo']; ?>"
  data-photo="<?php echo $student['photo_path']; ?>"
>
  ✏️
</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addStudentModalLabel">Dar de Alta Nuevo Alumno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?php echo URL; ?>Family/CreateStudent" method="POST">
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nombre Completo del Alumno:</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="matricula" class="form-label">Matrícula:</label>
                        <input type="text" class="form-control" id="matricula" name="matricula" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nivel" class="form-label">Nivel:</label>
                            <select class="form-select" id="nivel" name="nivel" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="Preescolar">Preescolar</option>
                                <option value="Primaria">Primaria</option>
                                <option value="Secundaria">Secundaria</option>
                                <option value="Preparatoria">Preparatoria</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="grado" class="form-label">Grado:</label>
                             <select class="form-select" id="grado" name="grado" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="1ro">1ro</option>
                                <option value="2do">2do</option>
                                <option value="3ro">3ro</option>
                                <option value="4to">4to</option>
                                <option value="5to">5to</option>
                                <option value="6to">6to</option>
                            </select>
                           
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="grupo" class="form-label">Grupo:</label>
                            <select class="form-select" id="grupo" name="grupo" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                
                            </select>
                           
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Alumno</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editStudentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Editar Alumno</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form 
        action="<?php echo URL; ?>Family/UpdateStudent" 
        method="POST" 
        enctype="multipart/form-data">

        <div class="modal-body">

          <input type="hidden" name="student_id" id="edit_student_id">

          <div class="text-center mb-3">
            <img 
              id="edit_student_photo"
              src="<?php echo URL; ?>Resource/images/student_default.png"
              class="rounded-circle"
              width="120"
              height="120"
              style="object-fit: cover;">
          </div>

          <div class="mb-3">
            <label class="form-label">Agregar o Cambiar foto</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
          </div>

          <div class="mb-3">
            <label>Nombre completo</label>
            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label>Nivel</label>
              <input type="text" name="nivel" id="edit_nivel" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label>Grado</label>
              <input type="text" name="grado" id="edit_grado" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label>Grupo</label>
              <input type="text" name="grupo" id="edit_grupo" class="form-control">
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success">Guardar cambios</button>
        </div>

      </form>

    </div>
  </div>
</div>
<script>
const editModal = document.getElementById('editStudentModal');

editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('edit_student_id').value = button.dataset.id;
    document.getElementById('edit_full_name').value = button.dataset.name;
    document.getElementById('edit_nivel').value = button.dataset.nivel;
    document.getElementById('edit_grado').value = button.dataset.grado;
    document.getElementById('edit_grupo').value = button.dataset.grupo;

    const photo = button.dataset.photo;
    document.getElementById('edit_student_photo').src = photo
        ? "<?php echo URL; ?>" + photo
        : "<?php echo URL; ?>Resource/images/student_default.png";
});
</script>