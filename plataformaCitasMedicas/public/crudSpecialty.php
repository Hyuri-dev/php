<?php

require_once '../config/database.php';
require_once '../src/controller/createSpecialty.php';
require_once '../src/controller/updateSpecialty.php';


if (isset($_POST['save'])) {

    // Recoge todos los datos
    $id = $_POST['id'] ?? '';
    $specialty = $_POST['name'] ?? '';

    // Decide si crear o actualizar
    if (!empty($id)) {
        // Si hay un ID, actualizamos
        $message = actualizarEspecialidad($conn, $id, $specialty);
    } else {
        // Si no hay ID, creamos
        $message = crearEspecialidad($conn,$specialty);
    }

    // Resetea el formulario POST si la operación fue exitosa
    if ($message === "Usuario creado exitosamente" || $message === "Usuario actualizado exitosamente") {
        $_POST = [];
    }
}
$allEspecialty = $conn->query("SELECT * FROM specialty")
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialidades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script>

        function editarEspecialidad(id, name){
            document.getElementById('id').value= id;
            document.getElementById('name').vale= name;
        }

        function limpiarFormulario() {
      document.getElementById('formUsers').reset(); 
      document.getElementById('id').value = ""; 
  }
</script>
</head>
<body>

<div class="container mt-4">
    <h2>Gestión de Especialidades</h2>
    
    <?php if (!empty($message)): ?>
        <p class="alert alert-info"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    
    <form method= "POST" id="specialtyForm" action="" class="mb-4">
      <input type="hidden" name="id" id="id"> 
      <input type="text" name="name" id="name" placeholder="Especialidad" required class="form-control mb-2"> <br><br>
      <button type="submit" name="save" class="btn btn-primary w-100">Guardar</button>
      <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button> 

      <table class="table table-bordered table-striped"">
      <thead>
        <tr>
            <th>Especialidad</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($allEspecialty as $aEspecialty) ?>
        <tr>
            <td> <?= htmlspecialchars($aEspecialty['name'])?></td>
            
        </tr>
      </tbody>
        
      </table>

      

    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>