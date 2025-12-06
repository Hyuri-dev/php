<?php

require_once '../config/database.php';
require_once '../src/controller/statusController.php';

session_start();
// Verificacion de sesión
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}



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
  <title>Gestión de usuarios</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />
<link rel="stylesheet" href="../styles/styles.css">

<script>
  function editarEspecialidad (id, status){
    document.getElementById('id').value = id; 
    document.getElementById('status').value = status;
  }

  function limpiarFormulario() {
      document.getElementById('statusForm').reset(); 
      document.getElementById('id').value = ""; 
  }
  // Temporizador de notificacion
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.transition = "opacity 0.5s ease";
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 4500);
</script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">


  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Inicio</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-user"></i> <?=htmlspecialchars($_SESSION['username'])?> <i class="fas fa-caret-down ml-1"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
          </a>
        </div>
      </li>
    </ul>
  </nav>


  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <i class="fa-solid fa-house-medical"></i>
      <span class="brand-text font-weight-light">Gestión Citas Medicas</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Usuarios</p>
            </a>
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="fa-solid fa-user-tie"></i>
              <p>Especialidad</p>
            </a>
            
            <a href="status.php" class="nav-link active">
              <i class="fa-solid fa-gear"></i>
              <p>Estados de las citas</p>
            </a>
            <!-- Pendiente por crear el apartado de las citas -->
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="fa-solid fa-calendar"></i>
              <p>Citas</p>
            </a>
            
          </li>
        </ul>
      </nav>
    </div>
  </aside>


  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Gestión de Estados</h1>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">


        <?php if ($message): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>


        <!-- Formulario -->
    <form action="" method="post" id="statusForm" class="mb-4">
        <input type="hidden" name="id" id="id"> 
        <input type="text" name="status" id="status" placeholder="Tipo de Estatus" require class="form-control mb-2"> <br><br>
        <button type="submit" name="save" class="btn btn-primary w-100">Ingresar</button>
        <button onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button>
    </form>


        <!-- Tabla -->
        <table class="table table-bordered table-hover mt-4">
          <thead class="thead-light">
            <tr>
              <th>Estado</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allStatus as $aStatus): ?>
              <tr>
              <td> <?= htmlspecialchars($aStatus['name'] ?? '') ?></td>
              
              <td> 
                  <button 
                    type="button" 
                    class="btn btn-sm btn-warning" 
                    onclick="editarEspecialidad(<?= $aStatus['id'] ?>, '<?= htmlspecialchars($aStatus['name'] ?? '') ?>')">
                      Editar
                  </button>
                                  <a href="crudStatus.php?action=delete&id=<?= $aStatus['id'] ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('¿Estás seguro de que quieres eliminar este estado?');">
                 Eliminar
              </a>
                  </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>


      </div>
    </section>
  </div>


  <!-- Footer -->
  <footer class="main-footer text-center bg-dark">
    <strong>&copy; 2025 ServiCare</strong> Todos los derechos reservados.
  </footer>


</div>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>


</body>
</html>