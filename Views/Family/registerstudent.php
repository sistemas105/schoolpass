<?php 
// Asegúrate de que esta vista sea cargada a través de un head/footer
// Si el controlador pasó datos, se accederán a través de $data
// $students = $data['students'] ?? []; 
?>

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
                <?php if (empty($data['students'])): // Si no hay alumnos (asumiendo que los pasas en $data['students']) ?>
                <tr>
                    <td colspan="7" class="text-center">Aún no hay alumnos registrados bajo esta familia.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($data['students'] as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['nivel']); ?></td>
                        <td><?php echo htmlspecialchars($student['grado']); ?></td>
                        <td><?php echo htmlspecialchars($student['grupo']); ?></td>
                        <td><?php echo $student['active'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white">✍️</button>
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
                            <input type="text" class="form-control" id="grado" name="grado" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="grupo" class="form-label">Grupo:</label>
                            <input type="text" class="form-control" id="grupo" name="grupo" required>
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