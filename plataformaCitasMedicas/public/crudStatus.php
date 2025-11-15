<?php

require_once '../config/database.php';
require_once '../src/controller/statusController.php';


$message ='';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {

    $id_a_eliminar = $_GET['id'];

    if (eliminarEstatus($conn , $id_a_eliminar)) {
        $message = "Estatus eliminada exitosamente";
    } else {
        $message = "Error al eliminar el Estatus";
    }

    // Limpiamos la URL para que no se re-elimine al recargar
    header("Location: crudStatus.php");
    exit;
}

if (isset($_POST['save'])) {

    // Recoge todos los datos
    $id = $_POST['id'] ?? '';
    $status_name = $_POST['status'] ?? '';

    // Decide si crear o actualizar
    if (!empty($id)) {
        // Si hay un ID, actualizamos
        $message = actualizarEstatus($conn, $id, $status_name);
    } else {
        // Si no hay ID, creamos
        $message = crearEstatus($conn,$status_name);
    }

    // Resetea el formulario POST si la operación fue exitosa
    if ($message === "Estatus creado exitosamente" || $message === "Estatus actualizado exitosamente") {
        $_POST = [];
    }
}
$allStatus = $conn->query("SELECT * FROM status")
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Estatus de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
        function editarStatus(id , status){
            document.getElementById('id').value = id;
            document.getElementById('status').value = status;
        }

        function limpiarFormulario(){
            document.getElementById('statusForm').reset()
            document.getElementById('id').value = ""; 

        }
    </script>

</head>
<body>
    <h1>Gestion de Estatus de Citas</h1>
    <?php if (!empty($message)): ?>
        <p class="alert alert-info"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="" method="post" id="statusForm" class="mb-4">
        <input type="hidden" name="id" id="id"> 
        <input type="text" name="status" id="status" placeholder="Tipo de Estatus" require class="form-control mb-2"> <br><br>
        <button type="submit" name="save" class="btn btn-primary w-100">Ingresar</button>
        <button onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
          <tr>
              <th>Estatus</th>
              <th>Acciones</th> </tr>
        </thead>
        <tbody>
          <?php foreach($allStatus as $aStatus): ?>
          <tr>
              <td> <?= htmlspecialchars($aStatus['name'] ?? '') ?></td>
              
              <td> 
                  <button 
                    type="button" 
                    class="btn btn-sm btn-warning" 
                    onclick="editarStatus(<?= $aStatus['id'] ?>, '<?= htmlspecialchars($aStatus['name'] ?? '') ?>')">
                      Editar
                  </button>
                                  <a href="crudStatus.php?action=delete&id=<?= $aStatus['id'] ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('¿Estás seguro de que quieres eliminar este status?');">
                 Eliminar
              </a>
                  </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
          
      </table>
    
</body>
</html>